<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    public $fillable = ['name','description','feat_img','sku','quantity','price','category','product_type','sale','sale_price','rent','rent_price','prod_video'];
}