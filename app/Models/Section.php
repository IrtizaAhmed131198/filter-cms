<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    protected $primaryKey = 'id';

    protected $fillable = [
        'page_id',
        'label',
        'slug',
        'value',
        'type',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }
}
