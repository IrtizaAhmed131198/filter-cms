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

    public function index()
    {
        return view('admin.banner.index');
    }

    public function getData(Request $request)
    {
        $query = Banner::orderBy('sort_order', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)
            ->addColumn('image', function ($row) {
                return '<img src="'.asset($row->image).'" width="120">';
            })
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleBannerStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_banner')) {
                    $actions .= '<a href="' . route('admin.banner.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit User">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_banner')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteBanner"
                                    data-id="' . $row->id . '" title="Delete User">
                                    <i class="la la-trash"></i>
                                </button>';
                }
                return $actions ?: '<span class="text-muted">No actions</span>';
            })
            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/banner/', 'banner');
        }

        $banner = Banner::create($data);

        log_activity('create', Banner::class, $banner->id, 'Created a new banner: ' . $banner->title ?? 'N/A', ['banner' => $banner->toArray()]);

        return redirect('admin/banner')->with('message', 'Banner added!');
    }

    public function show(Banner $banner)
    {
        return view('admin.banner.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        $oldData = $banner->toArray();

        if ($request->hasFile('image')) {
            $this->deleteFile($banner->image);
            $data['image'] = $this->uploadFile($request->file('image'), 'uploads/banner/', 'banner');
        }

        $banner->update($data);

        log_activity('update', Banner::class, $banner->id, 'Updated banner: ' . $banner->title ?? 'N/A', ['before' => $oldData, 'after' => $banner->toArray()]);

        return redirect()->route('admin.banner.index')->with('message', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        log_activity('delete', Banner::class, $banner->id, "Deleted banner {$banner->title}");
        return response()->json(['success' => 'Banner deleted successfully.']);
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->status = !$banner->status;
        $oldStatus = $banner->status;
        $banner->save();

        log_activity('status_toggle', Banner::class, $banner->id,
            'Toggled banner status for ' . $banner->name . ' from ' . ($oldStatus ? 'Active' : 'Inactive') . ' to ' . ($banner->status ? 'Active' : 'Inactive'),
            [
                'old_status' => $oldStatus,
                'new_status' => $banner->status
            ]
        );

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

        log_activity('restore', Banner::class, $id, "Restored banner {$banner->title}");

        return response()->json(['success' => 'Banner restored successfully!']);
    }

    public function forceDelete($id)
    {
        $banner = Banner::withTrashed()->findOrFail($id);

        if ($banner->image && File::exists(public_path($banner->image))) {
            File::delete(public_path($banner->image));
        }

        $banner->forceDelete();

        log_activity('force_delete', Banner::class, $id, "Banner {$banner->title} permenantly deleted");

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

        log_activity('bulk_delete', Banner::class, null, 'Bulk deleted banners: ' . implode(',', $ids), ['ids' => $ids]);

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

        log_activity('bulk_restore', Banner::class, null, 'Bulk restore banners: ' . implode(',', $ids), ['ids' => $ids]);

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

        log_activity('bulk_force_delete', Banner::class, null, 'Bulk permanently deleted banners: ' . implode(',', $ids), ['ids' => $ids]);

        foreach ($banners as $banner) {
            $this->deleteFile($banner->image);
            $banner->forceDelete();
        }

        return response()->json(['success' => 'Selected banners permanently deleted.']);
    }

    public function sort(Request $request)
    {
        $order = $request->input('order', []);
        if (!is_array($order) || empty($order)) {
            return response()->json([
                'success' => false,
                'message' => 'No order data received',
                'received' => $request->all()
            ], 400);
        }

        foreach ($order as $item) {
            // accept either 'position' or 'newPosition' for robustness
            $pos = $item['position'] ?? $item['newPosition'] ?? null;
            $id  = $item['id'] ?? null;

            if ($id === null || $pos === null) {
                // skip invalid entries
                continue;
            }

            Banner::where('id', $id)->update(['sort_order' => (int)$pos]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }
}
