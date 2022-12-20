<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Keywords extends Model
{
    protected $fillable = [
        'project_id', 'nama'
    ];

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }

    public function keyword_log(){
        return $this->hasMany(Keyword_log::class,'nama','nama');
    }
}
