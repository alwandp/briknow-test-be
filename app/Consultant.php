<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    protected $fillable = [
        'nama', 'website', 'telepon', 'email', 'facebook','instagram','lokasi','tentang','bidang'
    ];

    public function favorite_consultant(){
        return $this->hasMany(Favorite_consultant::class,'consultant_id');
    }

    public function project(){
        return $this->belongsToMany(Project::class,'consultant_projects','consultant_id','project_id');
    }
}
