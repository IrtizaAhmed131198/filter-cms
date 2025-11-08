<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Permission;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::select(['id', 'name', 'label', 'created_at']);
            return DataTables::of($data)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-danger delete-permission" data-id="'.$row->id.'">
                            <i class="la la-trash"></i> Delete
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            // Extract the module name (like "users", "banner", etc.)
            return ucfirst(explode('_', $permission->name)[1] ?? 'Other');
        });
        return view('admin.permissions.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'label' => 'nullable|string'
        ]);

        $permission = Permission::create($request->only('name', 'label'));
        $superAdmin = Role::where('name', 'super_admin')->first();

        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching([$permission->id]);
        }

        return back()->with('message', 'Permission created successfully.');
    }

    public function assignToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->permissions()->sync($request->permissions ?? []);

        return back()->with('message', 'Permissions assigned successfully.');
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();
            return response()->json(['success' => true, 'message' => 'Permission deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete permission.']);
        }
    }
    public function getRolePermissions($id)
    {
        $role = Role::with('permissions:id')->findOrFail($id);

        return response()->json([
            'permissions' => $role->permissions->pluck('id')
        ]);
    }
}
