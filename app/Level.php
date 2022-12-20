<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'name', 'xp', 'badge'
    ];

    public function avatar(){
        return $this->hasMany(avatar::class,'level_id');
    }
}
