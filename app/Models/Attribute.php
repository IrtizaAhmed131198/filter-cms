<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'status'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id');
    }
}
