<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Search_log extends Model
{
    protected $fillable = [
        'user_id','project_id'
    ];
    protected $with = ['project','user'];

    public function search(){
        return $this->belongsTo(Project::class,'project_id');
    }

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}