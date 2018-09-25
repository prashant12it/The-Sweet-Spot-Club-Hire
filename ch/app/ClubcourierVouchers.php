<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 25-09-2018
 * Time: 01:12 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;

class ClubcourierVouchers extends Model
{
    public $fillable = ['name','description','szCoupnCode','dt_from','dt_upto','offer_type','offer_percntg','offer_amnt','is_valid','isOneTimeOffer'];
}