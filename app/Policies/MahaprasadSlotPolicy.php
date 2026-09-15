<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MahaprasadSlot;
use Illuminate\Auth\Access\HandlesAuthorization;

class MahaprasadSlotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_mahaprasad::slot');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MahaprasadSlot $mahaprasadSlot): bool
    {
        return $user->can('view_mahaprasad::slot');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_mahaprasad::slot');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MahaprasadSlot $mahaprasadSlot): bool
    {
        return $user->can('update_mahaprasad::slot');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MahaprasadSlot $mahaprasadSlot): bool
    {
        return $user->can('delete_mahaprasad::slot');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_mahaprasad::slot');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, MahaprasadSlot $mahaprasadSlot): bool
    {
        return $user->can('force_delete_mahaprasad::slot');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_mahaprasad::slot');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, MahaprasadSlot $mahaprasadSlot): bool
    {
        return $user->can('restore_mahaprasad::slot');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_mahaprasad::slot');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, MahaprasadSlot $mahaprasadSlot): bool
    {
        return $user->can('replicate_mahaprasad::slot');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_mahaprasad::slot');
    }
}
