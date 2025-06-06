<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Group $group): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Group $group): bool
    {
        //
    }

    public function view(User $user, Group $group)
    {
        return $group->users->contains($user->id);
    }

    public function update(User $user, Group $group)
    {
        return $user->id === $group->admin_id;
    }

    public function delete(User $user, Group $group)
    {
        return $user->id === $group->admin_id;
    }

    public function manageGroup(User $user, Group $group)
    {
        return $user->id === $group->admin_id;
    }

}
