<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Facades\File;

class NewsObserver
{
    public function created(News $news): void
    {
        //
    }

    public function updated(News $news): void
    {
        if ($news->isDirty('image')) {
            $oldImage = $news->getOriginal('image');
            if ($oldImage && File::exists(public_path($oldImage))) {
                File::delete(public_path($oldImage));
            }
        }
    }

    public function deleted(News $news): void
    {
        if ($news->isForceDeleting()) {
            if ($news->image && File::exists(public_path($news->image))) {
                File::delete(public_path($news->image));
            }
        }
    }

    public function restored(News $news): void
    {
        //
    }

    public function forceDeleted(News $news): void
    {
        //
    }
}
