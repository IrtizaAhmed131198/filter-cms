<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.category.index');
    }

    public function getData(Request $request)
    {
        $query = Category::orderBy('id', 'desc');

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
            ->addColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-')
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleCategoryStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_category')) {
                    $actions .= '<a href="' . route('admin.category.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit Category">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_category')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteCategory"
                                    data-id="' . $row->id . '" title="Delete Category">
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
        return view('admin.category.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->only('name', 'description'));

        log_activity('create', Category::class, $category->id, 'Created a new category: ' . $category->name);

        return redirect()->route('admin.category.index')->with('message', 'Category added successfully!');
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $oldData = $category->toArray();

        $category->update($request->only('name', 'description'));

        log_activity('update', Category::class, $category->id, 'Updated category: ' . $category->name, [
            'before' => $oldData,
            'after'  => $category->toArray()
        ]);

        return redirect()->route('admin.category.index')->with('message', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        log_activity('delete', Category::class, $category->id, "Deleted category {$category->name}");

        return response()->json(['success' => 'Category deleted successfully.']);
    }

    public function toggleStatus(Category $category)
    {
        $oldStatus = $category->status;
        $category->status = !$category->status;
        $category->save();

        log_activity(
            'status_toggle',
            Category::class,
            $category->id,
            'Toggled category status for ' . $category->name . ' from ' . ($oldStatus ? 'Active' : 'Inactive') . ' to ' . ($category->status ? 'Active' : 'Inactive'),
            [
                'old_status' => $oldStatus,
                'new_status' => $category->status
            ]
        );

        return response()->json([
            'success' => true,
            'status' => $category->status ? 'Active' : 'Inactive',
        ]);
    }

    public function trash()
    {
        return view('admin.category.trash');
    }

    public function getTrashedData(Request $request)
    {
        $categories = Category::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $restore = '<button class="btn btn-sm btn-success restoreCategory" data-id="'.$row->id.'">
                                <i class="la la-refresh"></i> Restore
                            </button>';
                $delete = '<button class="btn btn-sm btn-danger forceDeleteCategory" data-id="'.$row->id.'">
                                <i class="la la-trash"></i> Delete Permanently
                            </button>';
                return $restore . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        log_activity('restore', Category::class, $id, "Restored category {$category->name}");

        return response()->json(['success' => 'Category restored successfully!']);
    }

    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();

        log_activity('force_delete', Category::class, $id, "Permanently deleted category {$category->name}");

        return response()->json(['success' => 'Category permanently deleted.']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Category::whereIn('id', $ids)->delete();

        log_activity('bulk_delete', Category::class, null, 'Bulk deleted categories: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected categories deleted successfully.']);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Category::withTrashed()->whereIn('id', $ids)->restore();

        log_activity('bulk_restore', Category::class, null, 'Bulk restored categories: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected categories restored successfully.']);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Category::withTrashed()->whereIn('id', $ids)->forceDelete();

        log_activity('bulk_force_delete', Category::class, null, 'Bulk permanently deleted categories: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected categories permanently deleted.']);
    }
}
