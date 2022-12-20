<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    protected $fillable = ['congrats_view', 'personal_number', 'level_before','level_after'];
    protected $with = ['levelbefore','levelafter'];
    public function levelbefore(){
        return $this->belongsto(Level::class,'level_before');
    }

    public function levelafter(){
        return $this->belongsto(Level::class,'level_after');
    }
}
