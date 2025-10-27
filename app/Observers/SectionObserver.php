<?php
namespace App\Observers;

use App\Models\Section;
use Illuminate\Support\Facades\File;

class SectionObserver
{
    public function deleting(Section $section)
    {
        if (in_array($section->type, ['image', 'video']) && File::exists(public_path($section->value))) {
            File::delete(public_path($section->value));
        }
    }
}
