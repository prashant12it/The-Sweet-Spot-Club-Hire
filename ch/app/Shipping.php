<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
class Shipping extends Model
{
    protected $table = 'shipping';
    public $fillable = ['postcode','shipping_cost','suburb','comments','region_id'];
}
