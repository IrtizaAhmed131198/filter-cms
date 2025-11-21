<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;

class AttributesValueController extends Controller
{
    public function index()
    {
        return view('admin.attributesvalue.index');
    }

    public function getData(Request $request)
    {
        $query = AttributeValue::with('attributes')->orderBy('id', 'desc');

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
            ->addColumn('attribute', fn($row) => $row->attributes->name ?? '-')
            ->addColumn('value', function ($row) {
                return $row->value;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-';
            })
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleAttributevalueStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_attribute_value')) {
                    $actions .= '<a href="' . route('admin.attributesvalue.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit Attribute Value">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_attribute_value')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteAttributevalue"
                                    data-id="' . $row->id . '" title="Delete Attribute Value">
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
        $attributes = Attribute::where('status', 1)->get();
        return view('admin.attributesvalue.create', compact('attributes'));
    }

    public function store(StoreAttributeValueRequest $request)
    {
        $data = $request->only('attribute_id', 'value');

        $attributeValue = AttributeValue::create($data);

        log_activity('create', AttributeValue::class, $attributeValue->id, 'Created a new attribute value: ' . $attributeValue->value, [
            'attribute_value' => $attributeValue->toArray()
        ]);

        return redirect()->route('admin.attributesvalue.index')->with('message', 'Attribute value added successfully!');
    }

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = Attribute::where('status', 1)->get();
        return view('admin.attributesvalue.edit', compact('attributeValue', 'attributes'));
    }

    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue)
    {
        $oldData = $attributeValue->toArray();

        $data = $request->only('attribute_id', 'value');

        $attributeValue->update($data);

        log_activity('update', AttributeValue::class, $attributeValue->id, 'Updated attribute value: ' . $attributeValue->value, [
            'before' => $oldData,
            'after'  => $attributeValue->toArray()
        ]);

        return redirect()->route('admin.attributesvalue.index')->with('message', 'Attribute value updated successfully!');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();

        log_activity('delete', AttributeValue::class, $attributeValue->id, "Deleted attribute value {$attributeValue->value}");

        return response()->json(['success' => 'Attribute value deleted successfully.']);
    }

    public function toggleStatus(AttributeValue $attributeValue)
    {
        $oldStatus = $attributeValue->status;

        $attributeValue->status = !$attributeValue->status;
        $attributeValue->save();

        log_activity(
            'status_toggle',
            AttributeValue::class,
            $attributeValue->id,
            'Toggled attribute value status for ' . $attributeValue->value . ' from ' . ($oldStatus ? 'Active' : 'Inactive') . ' to ' . ($attributeValue->status ? 'Active' : 'Inactive'),
            [
                'old_status' => $oldStatus,
                'new_status' => $attributeValue->status
            ]
        );

        return response()->json([
            'success' => true,
            'status' => $attributeValue->status ? 'Active' : 'Inactive',
        ]);
    }

    public function trash()
    {
        return view('admin.attributesvalue.trash');
    }

    public function getTrashedData(Request $request)
    {
        $attributesValue = AttributeValue::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($attributesValue)
            ->addColumn('attribute', fn($row) => $row->attributes->name ?? '-')
            ->addColumn('value', function ($row) {
                return $row->value;
            })
            ->addColumn('action', function ($row) {
                $restore = '<button class="btn btn-sm btn-success restoreAttributevalue" data-id="'.$row->id.'">
                                <i class="la la-refresh"></i> Restore
                            </button>';
                $delete = '<button class="btn btn-sm btn-danger forceDeleteAttributevalue" data-id="'.$row->id.'">
                                <i class="la la-trash"></i> Delete Permanently
                            </button>';
                return $restore . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function restore($id)
    {
        $attributeValue = AttributeValue::withTrashed()->findOrFail($id);
        $attributeValue->restore();

        log_activity('restore', AttributeValue::class, $id, "Restored attribute value {$attributeValue->value}");

        return response()->json(['success' => 'Attribute value restored successfully!']);
    }

    public function forceDelete($id)
    {
        $attributeValue = AttributeValue::withTrashed()->findOrFail($id);
        $attributeValue->forceDelete();

        log_activity('force_delete', AttributeValue::class, $id, "Permanently deleted attribute value {$attributeValue->value}");

        return response()->json(['success' => 'Attribute value permanently deleted.']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        AttributeValue::whereIn('id', $ids)->delete();

        log_activity('bulk_delete', AttributeValue::class, null, 'Bulk deleted attribute values: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected attribute values deleted successfully.']);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        AttributeValue::withTrashed()->whereIn('id', $ids)->restore();

        log_activity('bulk_restore', AttributeValue::class, null, 'Bulk restored attribute values: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected attribute values restored successfully.']);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        AttributeValue::withTrashed()->whereIn('id', $ids)->forceDelete();

        log_activity('bulk_force_delete', AttributeValue::class, null, 'Bulk permanently deleted attribute values: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected attribute values permanently deleted.']);
    }
}
