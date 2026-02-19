<?php

namespace App\Policies;

use App\Models\Fact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FactPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isModerator() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fact $fact): bool
    {
        return $user->isModerator() || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fact $fact): bool
    {
        return $user->id === $fact->user_id || $user->isModerator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fact $fact): bool
    {
        return $user->id === $fact->user_id || $user->isModerator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fact $fact): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fact $fact): bool
    {
        return false;
    }
}
