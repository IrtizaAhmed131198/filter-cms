<?php
namespace App\Repositories;

use App\Models\Page;
use Illuminate\Support\Facades\File;
use App\Traits\FileUploadTrait;

class PageRepository
{
    use FileUploadTrait;

    public function store(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->uploadFile($data['image'], 'uploads/pages/', 'page');
        }
        return Page::create($data);
    }

    public function update(Page $page, array $data)
    {
        if (isset($data['image'])) {
            if ($page->image && File::exists(public_path($page->image))) {
                File::delete(public_path($page->image));
            }
            $data['image'] = $this->uploadFile($data['image'], 'uploads/pages/', 'page');
        }
        return tap($page)->update($data);
    }

    public function delete(Page $page)
    {
        if ($page->image && File::exists(public_path($page->image))) {
            File::delete(public_path($page->image));
        }
        return $page->delete();
    }
}
