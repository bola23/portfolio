<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Base page for editing a single settings record (profile, website settings).
 *
 * @property-read Schema $form
 */
abstract class SingletonSettingsPage extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    abstract protected function record(): Model;

    /**
     * @return array<int, mixed>
     */
    abstract protected function formComponents(): array;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    public function mount(): void
    {
        $this->form->fill($this->record()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->formComponents())
            ->model($this->record())
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save changes')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $record = $this->record();
        $record->fill($this->form->getState())->save();

        $this->form->model($record)->saveRelationships();

        Notification::make()->success()->title('Saved')->body('The public website has been updated.')->send();
    }
}
