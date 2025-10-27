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
use App\Traits\FileUploadTrait;

class UsersController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return view('admin.users.index');
    }

    public function getData(Request $request)
    {
        $users = User::with('roles:id,name', 'profile')
                    ->orderByDesc('id')
                    ->get();

        return DataTables::of($users)
            ->addColumn('profile_image', function ($row) {
                return $row->profile && $row->profile->image
                    ? '<img src="' . asset($row->profile->image) . '" width="60" class="rounded-circle">'
                    : '<span class="text-muted">No Image</span>';
            })
            ->addColumn('role', function ($row) {
                return $row->role->pluck('name') ?: '<span class="text-muted">No Role</span>';
            })
            ->addColumn('status', function ($row) {
                $badgeClass = $row->status ? 'success' : 'danger';
                $text = $row->status ? 'Active' : 'Inactive';
                return '<button class="btn btn-sm btn-' . $badgeClass . ' toggleUserStatus" data-id="' . $row->id . '">' . $text . '</button>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="' . route('admin.users.edit', $row->id) . '" class="btn btn-sm btn-info">
                            <i class="la la-pencil"></i>
                        </a>';
                $delete = '<button class="btn btn-sm btn-danger deleteUser" data-id="' . $row->id . '">
                            <i class="la la-trash"></i>
                        </button>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['profile_image', 'role', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        return view('admin.users.create');
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

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return redirect('admin/users')->with('flash_message', 'User added successfully!');
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
        $user = User::with('roles')->select('id', 'name', 'email')->findOrFail($id);

        return view('admin.users.edit', compact('user'));
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
        $data = $request->validated();

        // Handle password only if provided
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user = User::findOrFail($id);
        $user->update($data);

        return redirect('admin/users')->with('flash_message', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id)
    {
        User::destroy($id);

        return redirect('admin/users')->with('flash_message', 'User deleted!');
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

}
