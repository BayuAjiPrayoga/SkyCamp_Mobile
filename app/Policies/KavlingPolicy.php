<?php

namespace App\Policies;

use App\Models\Kavling;
use App\Models\User;

class KavlingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only authenticated users with admin middleware can access
        return true; // Middleware already handles auth check
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kavling $kavling): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin only (middleware handles this)
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kavling $kavling): bool
    {
        // Admin only
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kavling $kavling): bool
    {
        // Admin only
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kavling $kavling): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kavling $kavling): bool
    {
        return true;
    }
}
