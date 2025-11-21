<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;

class AttributeController extends Controller
{
    public function index()
    {
        return view('admin.attribute.index');
    }

    public function getData(Request $request)
    {
        $query = Attribute::orderBy('id', 'desc');

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
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-';
            })
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleAttributeStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_attribute')) {
                    $actions .= '<a href="' . route('admin.attribute.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit Attribute">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_attribute')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteAttribute"
                                    data-id="' . $row->id . '" title="Delete Attribute">
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
        return view('admin.attribute.create');
    }

    public function store(StoreAttributeRequest $request)
    {
        $data = $request->only('name');

        $attribute = Attribute::create($data);

        log_activity('create', Attribute::class, $attribute->id, 'Created a new attribute: ' . $attribute->name, [
            'attribute' => $attribute->toArray()
        ]);

        return redirect()->route('admin.attribute.index')->with('message', 'Attribute added successfully!');
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.attribute.edit', compact('attribute'));
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $oldData = $attribute->toArray();

        $data = $request->only('name');

        $attribute->update($data);

        log_activity('update', Attribute::class, $attribute->id, 'Updated attribute: ' . $attribute->name, [
            'before' => $oldData,
            'after'  => $attribute->toArray()
        ]);

        return redirect()->route('admin.attribute.index')->with('message', 'Attribute updated successfully!');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        log_activity('delete', Attribute::class, $attribute->id, "Deleted attribute {$attribute->name}");

        return response()->json(['success' => 'Attribute deleted successfully.']);
    }

    public function toggleStatus(Attribute $attribute)
    {
        $oldStatus = $attribute->status;

        $attribute->status = !$attribute->status;
        $attribute->save();

        log_activity(
            'status_toggle',
            Attribute::class,
            $attribute->id,
            'Toggled attribute status for ' . $attribute->name . ' from ' . ($oldStatus ? 'Active' : 'Inactive') . ' to ' . ($attribute->status ? 'Active' : 'Inactive'),
            [
                'old_status' => $oldStatus,
                'new_status' => $attribute->status
            ]
        );

        return response()->json([
            'success' => true,
            'status' => $attribute->status ? 'Active' : 'Inactive',
        ]);
    }

    public function trash()
    {
        return view('admin.attribute.trash');
    }

    public function getTrashedData(Request $request)
    {
        $attributes = Attribute::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($attributes)
            ->addColumn('action', function ($row) {
                $restore = '<button class="btn btn-sm btn-success restoreAttribute" data-id="'.$row->id.'">
                                <i class="la la-refresh"></i> Restore
                            </button>';
                $delete = '<button class="btn btn-sm btn-danger forceDeleteAttribute" data-id="'.$row->id.'">
                                <i class="la la-trash"></i> Delete Permanently
                            </button>';
                return $restore . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function restore($id)
    {
        $attribute = Attribute::withTrashed()->findOrFail($id);
        $attribute->restore();

        log_activity('restore', Attribute::class, $id, "Restored attribute {$attribute->name}");

        return response()->json(['success' => 'Attribute restored successfully!']);
    }

    public function forceDelete($id)
    {
        $attribute = Attribute::withTrashed()->findOrFail($id);
        $attribute->forceDelete();

        log_activity('force_delete', Attribute::class, $id, "Permanently deleted attribute {$attribute->name}");

        return response()->json(['success' => 'Attribute permanently deleted.']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Attribute::whereIn('id', $ids)->delete();

        log_activity('bulk_delete', Attribute::class, null, 'Bulk deleted attributes: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected attributes deleted successfully.']);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Attribute::withTrashed()->whereIn('id', $ids)->restore();

        log_activity('bulk_restore', Attribute::class, null, 'Bulk restored attributes: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected attributes restored successfully.']);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        Attribute::withTrashed()->whereIn('id', $ids)->forceDelete();

        log_activity('bulk_force_delete', Attribute::class, null, 'Bulk permanently deleted attributes: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected attributes permanently deleted.']);
    }
}
