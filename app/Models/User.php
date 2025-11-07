<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    public function roleInfo()
    {
        return $this->hasOne(Role::class, 'id', 'role');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    public function hasPermission($permission)
    {
        $permissionName = is_string($permission) ? $permission : $permission->name;

        $role = $this->role()->with('permissions')->first();

        // Super Admin always has all permissions
        if ($role && $role->name === 'super_admin') {
            return true;
        }

        // Check user direct permissions (if you use them)
        if (isset($this->permissions) && $this->permissions->contains('name', $permissionName)) {
            return true;
        }

        // Check role permissions
        if ($role && $role->permissions->contains('name', $permissionName)) {
            return true;
        }

        return false;
    }

    public function givePermissionTo($permission)
    {
        $permissionModel = Permission::where('name', $permission)->firstOrFail();
        $this->permissions()->syncWithoutDetaching([$permissionModel->id]);
    }

    public function revokePermission($permission)
    {
        $permissionModel = Permission::where('name', $permission)->first();
        if ($permissionModel) {
            $this->permissions()->detach($permissionModel->id);
        }
    }

    // public function permissionsList(){
    //     $roles = $this->roles;
    //     $permissions = [];
    //     foreach ($roles as $role){
    //         $permissions[] = $role->permissions()->pluck('name')->implode(',');
    //     }
    //    return collect($permissions);
    // }

    // public function permissions(){
    //     $permissions = [];
    //     $role = $this->roles->first();
    //     $permissions = $role->permissions()->get();
    //     return $permissions;
    // }

    // public function isAdmin(){
    //    $is_admin =$this->roles()->where('name','admin')->first();
    //    if($is_admin != null){
    //        $is_admin = true;
    //    }else{
    //        $is_admin = false;
    //    }
    //    return $is_admin;
    // }
}
