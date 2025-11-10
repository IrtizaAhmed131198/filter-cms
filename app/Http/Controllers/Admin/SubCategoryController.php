<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index()
    {
        return view('admin.subcategory.index');
    }

    public function getData(Request $request)
    {
        $data = SubCategory::with('category')->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $data->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $data->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        return DataTables::of($data)
            ->addColumn('category', fn($row) => $row->category->name ?? '-')
            ->addColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-')
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleSubcategoryStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_subcategory')) {
                    $actions .= '<a href="' . route('admin.subcategory.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit SubCategory">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_subcategory')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteSubcategory"
                                    data-id="' . $row->id . '" title="Delete SubCategory">
                                    <i class="la la-trash"></i>
                                </button>';
                }
                return $actions ?: '<span class="text-muted">No actions</span>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.subcategory.create', compact('categories'));
    }

    public function store(StoreSubCategoryRequest $request)
    {
        $subcategory = SubCategory::create($request->validated());

        log_activity('create', SubCategory::class, $subcategory->id, 'Created sub-category: ' . $subcategory->name);

        return redirect()->route('admin.subcategory.index')->with('message', 'Sub-category added successfully!');
    }

    public function edit(SubCategory $subcategory)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.subcategory.edit', compact('subcategory', 'categories'));
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $subcategory)
    {
        $oldData = $subcategory->toArray();

        $subcategory->update($request->validated());

        log_activity('update', SubCategory::class, $subcategory->id, 'Updated sub-category: ' . $subcategory->name, [
            'before' => $oldData,
            'after'  => $subcategory->toArray()
        ]);

        return redirect()->route('admin.subcategory.index')->with('message', 'Sub-category updated successfully!');
    }

    public function destroy(SubCategory $subcategory)
    {
        $oldData = $subcategory->toArray();

        $subcategory->delete();

        log_activity('delete', SubCategory::class, $subcategory->id, 'Moved sub-category to trash: ' . $subcategory->name, [
            'before' => $oldData
        ]);

        return response()->json(['success' => true]);
    }

    public function trash()
    {
        return view('admin.subcategory.trash');
    }

    public function getTrashedData(Request $request)
    {
        $data = SubCategory::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($data)
            ->addColumn('category', fn($row) => $row->category->name ?? '-')
            ->addColumn('action', function ($row) {
                $restore = '<button class="btn btn-sm btn-success restoreSubCategory" data-id="'.$row->id.'">
                                <i class="la la-refresh"></i> Restore
                            </button>';
                $delete = '<button class="btn btn-sm btn-danger forceDeleteSubCategory" data-id="'.$row->id.'">
                                <i class="la la-trash"></i> Delete Permanently
                            </button>';
                return $restore . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function toggleStatus(SubCategory $subcategory)
    {
        $oldStatus = $subcategory->status;
        $subcategory->status = !$subcategory->status;
        $subcategory->save();

        log_activity('status_toggle', SubCategory::class, $subcategory->id,
            'Toggled status for ' . $subcategory->name . ' from ' . ($oldStatus ? 'Active' : 'Inactive') . ' to ' . ($subcategory->status ? 'Active' : 'Inactive')
        );

        return response()->json(['success' => true, 'status' => $subcategory->status ? 'Active' : 'Inactive']);
    }

    public function restore($id)
    {
        $subcategory = SubCategory::onlyTrashed()->findOrFail($id);
        $subcategory->restore();

        log_activity('restore', SubCategory::class, $subcategory->id, 'Restored sub-category: ' . $subcategory->name);

        return response()->json(['success' => true]);
    }

    public function forceDelete($id)
    {
        $subcategory = SubCategory::onlyTrashed()->findOrFail($id);
        $subcategory->forceDelete();

        log_activity('force_delete', SubCategory::class, $subcategory->id, 'Permanently deleted sub-category: ' . $subcategory->name);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        SubCategory::whereIn('id', $ids)->delete();

        log_activity('bulk_delete', SubCategory::class, null, 'Bulk deleted sub-categories: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected sub-categories deleted successfully.']);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        SubCategory::withTrashed()->whereIn('id', $ids)->restore();

        log_activity('bulk_restore', SubCategory::class, null, 'Bulk restored sub-categories: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected sub-categories restored successfully.']);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        SubCategory::withTrashed()->whereIn('id', $ids)->forceDelete();

        log_activity('bulk_force_delete', SubCategory::class, null, 'Bulk permanently deleted sub-categories: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected sub-categories permanently deleted.']);
    }
}
