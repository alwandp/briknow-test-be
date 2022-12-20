<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    protected $table = 'user_achievements';

    protected $fillable = ['personal_number', 'achievements_id', 'congrats_view'];

    protected $with = ['achievement'];

    public function achievement(){
        return $this->belongsto(Achievement::class,'achievements_id');
    }
}
