<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offers extends Model
{
    public $fillable = ['name','description','szCoupnCode','dt_from','dt_upto','offer_type','offer_percntg','offer_amnt','is_valid','isOneTimeOffer'];
}