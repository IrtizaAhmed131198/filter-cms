<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\File;

class UserObserver
{
    public function deleting(User $user)
    {
        if (! $user->isForceDeleting()) {
            // ✅ Soft delete the profile
            if ($user->profile) {
                $user->profile->delete();
            }
        }
    }

    public function restoring(User $user)
    {
        // ✅ Restore profile if user restored
        if ($user->profile()->withTrashed()->exists()) {
            $user->profile()->withTrashed()->restore();
        }
    }

    public function forceDeleted(User $user)
    {
        // ✅ Permanently remove related profile and picture
        if ($user->profile()->withTrashed()->exists()) {
            $profile = $user->profile()->withTrashed()->first();

            if ($profile->pic && File::exists(public_path($profile->pic))) {
                File::delete(public_path($profile->pic));
            }

            $profile->forceDelete();
        }
    }
}
