<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $guarded= [];

    protected $fillable = [
        'user_id', 'dob', 'bio', 'gender', 'country', 'state', 'city', 'address', 'postal', 'pic'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
