<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite_consultant extends Model
{
    protected $fillable = [
        'user_id', 'consultant_id'
    ];

    protected $with = [
        'user', 'consultant'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function consultant(){
        return $this->belongsto(Consultant::class,'consultant_id');
    }
}
