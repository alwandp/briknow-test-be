<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User_log extends Model
{
    protected $fillable = [
        'user_id','ip_address', 'country_name', 'country_code', 'region_code', 'city_name', 'zip_code', 'latitude', 'longitude', 'area_code'
    ];

    public function user(){
        return $this->BelongsTo(User::class,'user_id');
    }
}