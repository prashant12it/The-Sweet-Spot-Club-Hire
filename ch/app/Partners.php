<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partners extends Model
{
    public $fillable = [
                            'reference_id',
                            'name',
                            'email',
                            'password',
                            'contact_no',
                            'address',
                            'zipcode',
                            'state',
                            'country',
                            'iActive',
                        ];
}
