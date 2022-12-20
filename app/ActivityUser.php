<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityUser extends Model
{
    protected $fillable = ['activity_id', 'personal_number', 'xp_before','xp_after'];
    protected $with = ['activity'];

    public function activity(){
        return $this->belongsto(Activity::class,'activity_id');
    }
}
