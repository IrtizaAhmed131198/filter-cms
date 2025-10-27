<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Traits\FileUploadTrait;

class PageController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return view('admin.page.index');
    }

    public function getData(Request $request)
    {
        $pages = Page::orderByDesc('id')->get();

        return DataTables::of($pages)
            ->addColumn('action', function ($row) {
                $edit = '<a href="' . route('admin.pages.edit', $row->id) . '" class="btn btn-sm btn-info"><i class="la la-pencil"></i></a>';
                $delete = '<button class="btn btn-sm btn-danger deletePages" data-id="' . $row->id . '"><i class="la la-trash"></i></button>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new page
     */
    public function create()
    {
        return view('admin.page.create');
    }

    /**
     * Store a newly created page
     */
    public function store(StorePageRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/pages/', 'page');
        }

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('message', 'Page created successfully!');
    }

    /**
     * Show a specific page
     */
    public function show(Page $page)
    {
        return view('admin.page.show', compact('page'));
    }

    /**
     * Show the form for editing a specific page
     */
    public function edit(Page $page)
    {
        $page->load('sections');
        return view('admin.page.edit', compact('page'));
    }

    /**
     * Update a specific page and its sections
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $data = $request->validated();

        // Handle main image
        if ($request->hasFile('image')) {
            if ($page->image && File::exists(public_path($page->image))) {
                File::delete(public_path($page->image));
            }
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/pages/', 'page');
        }

        $page->update($data);

        // Update sections
        foreach ($page->sections as $section) {
            $slug = $section->slug;

            if ($section->type === 'image' && $request->hasFile($slug)) {
                if ($section->value && File::exists(public_path($section->value))) {
                    File::delete(public_path($section->value));
                }

                $path = $this->uploadFile($request->file($slug), 'uploads/pages/', $slug);
                $section->update(['value' => $path]);
            }
            elseif ($section->type === 'video' && $request->hasFile($slug)) {
                if ($section->value && File::exists(public_path($section->value))) {
                    File::delete(public_path($section->value));
                }

                $file = $request->file($slug);
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileNameToStore = $fileName . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/videos/'), $fileNameToStore);
                $section->update(['value' => 'uploads/videos/' . $fileNameToStore]);
            }
            elseif ($request->filled($slug)) {
                $section->update(['value' => $request->input($slug)]);
            }
        }

        return redirect()->back()->with('message', 'Page updated successfully!');
    }

    /**
     * Soft delete or permanently delete page
     */
    public function destroy(Page $page)
    {
        if ($page->image && File::exists(public_path($page->image))) {
            File::delete(public_path($page->image));
        }

        $page->delete();

        return response()->json(['success' => 'Page deleted successfully.']);
    }
}
