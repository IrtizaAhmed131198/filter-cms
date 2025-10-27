<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Traits\FileUploadTrait;

class BannerController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        return view('admin.banner.index');
    }

    public function getData(Request $request)
    {
        $banners = Banner::orderByDesc('id')->get();

        return DataTables::of($banners)
            ->addColumn('image', function ($row) {
                return '<img src="'.asset($row->image).'" width="120">';
            })
            ->addColumn('status', function ($row) {
                $badgeClass = $row->status ? 'success' : 'danger';
                $text = $row->status ? 'Active' : 'Inactive';
                return '<button class="btn btn-sm btn-' . $badgeClass . ' toggleBannerStatus" data-id="' . $row->id . '">' . $text . '</button>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.url('admin/banner/'.$row->id.'/edit').'" class="btn btn-sm btn-info"><i class="la la-pencil"></i></a>';
                $delete = '<button class="btn btn-sm btn-danger deleteBanner" data-id="'.$row->id.'"><i class="la la-trash"></i></button>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/banner/', 'banner');
        }

        Banner::create($data);

        return redirect('admin/banner')->with('message', 'Banner added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show(Banner $banner)
    {
        return view('admin.banner.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteFile($banner->image);
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/banner/', 'banner');
        }

        $banner->update($data);

        return redirect()->route('admin.banner.index')->with('message', 'Banner updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        return response()->json(['success' => 'Banner deleted successfully.']);
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->status = !$banner->status;
        $banner->save();

        return response()->json([
            'success' => true,
            'status' => $banner->status ? 'Active' : 'Inactive',
        ]);
    }

    public function trash()
    {
        return view('admin.banner.trash');
    }

    public function getTrashedData(Request $request)
    {
        $banners = Banner::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($banners)
            ->addColumn('image', function ($row) {
                return '<img src="'.asset($row->image).'" width="120">';
            })
            ->addColumn('action', function ($row) {
                $restore = '<button class="btn btn-sm btn-success restoreBanner" data-id="'.$row->id.'">
                                <i class="la la-refresh"></i> Restore
                            </button>';
                $delete = '<button class="btn btn-sm btn-danger forceDeleteBanner" data-id="'.$row->id.'">
                                <i class="la la-trash"></i> Delete Permanently
                            </button>';
                return $restore . ' ' . $delete;
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $banner = Banner::withTrashed()->findOrFail($id);
        $banner->restore();

        return response()->json(['success' => 'Banner restored successfully!']);
    }

    public function forceDelete($id)
    {
        $banner = Banner::withTrashed()->findOrFail($id);

        if ($banner->image && File::exists(public_path($banner->image))) {
            File::delete(public_path($banner->image));
        }

        $banner->forceDelete();

        return response()->json(['success' => 'Banner permanently deleted.']);
    }

    // Bulk delete (soft delete)
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Banner::whereIn('id', $ids)->delete();

        return response()->json(['success' => 'Selected banners deleted successfully.']);
    }

    // Bulk restore from trash
    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Banner::withTrashed()->whereIn('id', $ids)->restore();

        return response()->json(['success' => 'Selected banners restored successfully.']);
    }

    // Bulk permanent delete (from trash)
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        $banners = Banner::withTrashed()->whereIn('id', $ids)->get();

        foreach ($banners as $banner) {
            $this->deleteFile($banner->image);
            $banner->forceDelete();
        }

        return response()->json(['success' => 'Selected banners permanently deleted.']);
    }
}
