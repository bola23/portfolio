<?php

namespace Tests;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Public pages render built assets; tests must not depend on a Vite build.
        $this->withoutVite();
    }

    protected function signInAsAdmin(): User
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        return $admin;
    }
}
