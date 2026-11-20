<?php

namespace App\Policies;

use App\Models\Shortener;
use App\Models\User;

class ShortenerPolicy
{
    /**
     * Determine whether the user can view the shortener.
     */
    public function view(?User $user, Shortener $shortener): bool
    {
        // Public shorteners can be viewed by anyone
        // Private shorteners only by their owner
        return $shortener->UserID === null || ($user && $shortener->UserID === $user->id);
    }

    /**
     * Determine whether the user can update the shortener.
     */
    public function update(User $user, Shortener $shortener): bool
    {
        return $user->id === $shortener->UserID;
    }

    /**
     * Determine whether the user can delete the shortener.
     */
    public function delete(User $user, Shortener $shortener): bool
    {
        return $user->id === $shortener->UserID;
    }

    /**
     * Determine whether the user can restore the shortener.
     */
    public function restore(User $user, Shortener $shortener): bool
    {
        return $user->id === $shortener->UserID;
    }

    /**
     * Determine whether the user can permanently delete the shortener.
     */
    public function forceDelete(User $user, Shortener $shortener): bool
    {
        return $user->id === $shortener->UserID;
    }
}
