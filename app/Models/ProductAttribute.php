<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';

    protected $fillable = ['attribute_id', 'value', 'qty', 'price', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
