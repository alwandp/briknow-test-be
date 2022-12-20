<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name', 'badge', 'activity_id', 'value'
    ];

    protected $with = [
        'activity'
    ];

    public function activity(){
        return $this->belongsTo(Activity::class,'activity_id');
    }
}
