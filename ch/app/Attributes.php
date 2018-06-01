<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    public $timestamps = false;
    public $fillable = ['attrib_name','attrib_id','value'];
}
