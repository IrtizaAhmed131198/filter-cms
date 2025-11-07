<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\imagetable;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\FileUploadTrait;
use App\Mail\UserCreatedMail;
use App\Mail\UserStatusChangedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        $total_users = User::where('role', '!=', 1)->count();
        $active_users = User::where('role', '!=', 1)->where('status', 1)->count();
        $inactive_users = User::where('role', '!=', 1)->where('status', 0)->count();
        $roles = Role::where('name', '!=', 'super_admin')->get();
        return view('admin.users.index', compact('total_users', 'active_users', 'inactive_users', 'roles'));
    }

    public function getData(Request $request)
    {
        $query = User::where('role', '!=', 1)
                    ->with(['roleInfo', 'profile'])
                    ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)
            ->addColumn('role', function ($row) {
                return ($row->roleInfo && $row->roleInfo->name)
                    ? $row->roleInfo->name
                    : '<span class="text-muted">No Role</span>';
            })
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleUsersStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-';
            })
            ->addColumn('image', function ($row) {
                $profileImage = ($row->profile && $row->profile->pic) ? $row->profile->pic : 'assets/imgs/default.png';
                $imageUrl = asset($profileImage);

                return '<img
                            data-src="' . $imageUrl . '"
                            src="' . asset('assets/imgs/placeholder.jpg') . '"
                            class="lazy-image rounded-circle"
                            width="80"
                            height="70"
                            alt="User Image"
                        />';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_users')) {
                    $actions .= '<a href="' . route('admin.users.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit User">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_users')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteUsers"
                                    data-id="' . $row->id . '" title="Delete User">
                                    <i class="la la-trash"></i>
                                </button>';
                }
                return $actions ?: '<span class="text-muted">No actions</span>';
            })

            ->rawColumns(['status', 'action', 'image'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'super_admin')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // 1️⃣ Create user
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        // 2️⃣ Handle profile data
        $profileData = [
            'dob'     => $request->dob,
            'bio'     => $request->bio,
            'gender'  => $request->gender,
            'country' => $request->country,
            'state'   => $request->state,
            'city'    => $request->city,
            'address' => $request->address,
            'postal'  => $request->postal,
        ];

        // 3️⃣ Handle file upload (pic)
        if ($request->hasFile('pic')) {
            $profileData['pic'] = $this->uploadFile($request->file('pic'), 'uploads/profile_pics/', 'users');
        }

        // 4️⃣ Save profile
        $user->profile()->create($profileData);

        log_activity('create', User::class, $user->id, 'Created a new user: ' . $user->name, ['user' => $user->only(['id', 'name', 'email', 'role'])]);

        // 6️⃣ Send email notification to new user
        Mail::to($user->email)->send(new UserCreatedMail($user));

        return redirect('admin/users')->with('message', 'User added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit($id)
    {
        $user = User::with('roleInfo', 'profile')->findOrFail($id);
        $roles = Role::where('name', '!=', 'super_admin')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $id
     *
     * @return void
     */

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        $oldData = $user->toArray();

        // ✅ Update user fields
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // ✅ Handle profile data
        $profileData = $request->only([
            'dob', 'bio', 'gender', 'country', 'state', 'city', 'address', 'postal'
        ]);

        if ($request->hasFile('pic')) {
            $profileData['pic'] = $this->uploadFile($request->file('pic'), 'uploads/profile_pics/', 'users');
        }

        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        log_activity('update', User::class, $user->id,
            'Updated user: ' . $user->name,
            [
                'before' => $oldData,
                'after' => $user->toArray()
            ]
        );

        return redirect()->route('admin.users.index')->with('message', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(User $user)
    {
        $user->delete();
        log_activity('delete', User::class, $user->id, "Deleted user {$user->name}");
        return response()->json(['success' => 'User deleted successfully.']);
    }

    public function getSettings(){
        $user = auth()->user();
        return view('admin.users.account-settings',compact('user'));
    }

    public function saveSettings(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required',
        ]);

        $user =  auth()->user();

        if($request->password){
            $user->password = bcrypt($request->password);
        }
        $user->email = $request->email;
        $user->name = $request->name;
        $user->save();

        $profile = $user->profile;
        if($user->profile == null){
            $profile = new  Profile();
        }
        if($request->dob != null){
            $date =  Carbon::parse($request->dob)->format('Y-m-d');
        }else{
            $date = $request->dob;
        }


        if ($file = $request->file('pic_file')) {
            $extension = $file->extension()?: 'png';
            $destinationPath = public_path() . '/storage/uploads/users/';
            $safeName = str_random(10) . '.' . $extension;
            $file->move($destinationPath, $safeName);
            //delete old pic if exists
            if (File::exists($destinationPath . $user->pic)) {
                File::delete($destinationPath . $user->pic);
            }
            //save new file path into db
            $profile->pic = $safeName;
        }


        $profile->user_id = $user->id;
        $profile->bio = $request->bio;
        $profile->gender = $request->gender;
        $profile->dob = $date;
        $profile->country = $request->country;
        $profile->state = $request->state;
        $profile->city = $request->city;
        $profile->address = $request->address;
        $profile->postal = $request->postal;
        $profile->save();

        Session::flash('message','Account has been updated');
        return redirect()->back();
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $oldStatus = $user->status;
        $user->status = !$user->status;
        $user->save();

        log_activity('status_toggle', User::class, $user->id,
            'Toggled user status for ' . $user->name . ' from ' . ($oldStatus ? 'Active' : 'Inactive') . ' to ' . ($user->status ? 'Active' : 'Inactive'),
            [
                'old_status' => $oldStatus,
                'new_status' => $user->status
            ]
        );

        // Mail::to($user->email)->send(new UserStatusChangedMail($user));

        return response()->json([
            'success' => true,
            'status' => $user->status ? 'Active' : 'Inactive',
        ]);
    }

    public function trash()
    {
        return view('admin.users.trash');
    }

    public function getTrashedData(Request $request)
    {
        $users = User::onlyTrashed()->orderByDesc('id')->get();

        return DataTables::of($users)
            ->addColumn('role', function ($row) {
                return ($row->roleInfo && $row->roleInfo->name) ? $row->roleInfo->name : '<span class="text-muted">No Role</span>';
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
            ->rawColumns(['action'])
            ->make(true);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        log_activity('restore', User::class, $id, "Restored user {$user->name}");

        return response()->json(['success' => 'User restored successfully!']);
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->image && File::exists(public_path($user->image))) {
            File::delete(public_path($user->image));
        }

        $user->forceDelete();

        log_activity('force_delete', User::class, $id, "User {$user->name} permenantly deleted");

        return response()->json(['success' => 'User permanently deleted.']);
    }

    // Bulk delete (soft delete)
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        User::whereIn('id', $ids)->delete();

        log_activity('bulk_delete', User::class, null, 'Bulk deleted users: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected users deleted successfully.']);
    }

    // Bulk restore from trash
    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        User::withTrashed()->whereIn('id', $ids)->restore();

        log_activity('bulk_restore', User::class, null, 'Bulk restored users: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected users restored successfully.']);
    }

    // Bulk permanent delete (from trash)
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        $users = User::withTrashed()->whereIn('id', $ids)->get();

        foreach ($users as $user) {
            $this->deleteFile($user->image);
            $user->forceDelete();
        }

        log_activity('bulk_force_delete', User::class, null, 'Permanently deleted users: ' . implode(',', $ids), ['ids' => $ids]);

        return response()->json(['success' => 'Selected users permanently deleted.']);
    }

}
