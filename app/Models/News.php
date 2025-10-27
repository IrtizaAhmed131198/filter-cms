<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $table = 'news';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description', 'image', 'status'];
    protected $dates = ['deleted_at'];
}