<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $fillable = [
        'banner_reference_id',
        'title',
        'width',
        'height',
        'file_name',
        'iActive',
        'created_at',
        'updated_at',
        'url_val',
        'banner_type'
    ];
}
