<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('portfolio:make-admin
    {--name= : Display name of the administrator}
    {--email= : Login email address}
    {--password= : Password (omit to be prompted securely)}')]
#[Description('Create a portfolio administrator, or promote an existing user with the same email.')]
class MakeAdminCommand extends Command
{
    public function handle(): int
    {
        $name = $this->option('name') ?: text('Name', required: true, default: 'Administrator');
        $email = $this->option('email') ?: text('Email', required: true, validate: ['email' => 'email']);
        $password = $this->option('password') ?: password('Password (min. 12 characters)', required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', Password::min(12)->letters()->numbers()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill(['name' => $name, 'password' => $password]);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        $this->components->info(sprintf(
            'Administrator %s %s. Sign in at %s',
            $email,
            $user->wasRecentlyCreated ? 'created' : 'updated',
            url('/admin'),
        ));

        return self::SUCCESS;
    }
}
