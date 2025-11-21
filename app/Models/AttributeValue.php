<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use SoftDeletes;

    protected $table = 'attributes_values';

    protected $fillable = ['attribute_id', 'value'];

    public function attributes()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

}
