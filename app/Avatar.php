<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    protected $fillable = [
        'level_id', 'path'
    ];

    protected $with = [
        'level'
    ];

    public function level(){
        return $this->belongsTo(level::class,'level_id');
    }
}
