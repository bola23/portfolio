<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Messages are only created by visitors through the public contact form.
 */
class ContactMessagePolicy extends AdminContentPolicy
{
    public function create(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Model $model): bool
    {
        return false;
    }
}
