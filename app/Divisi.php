<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = [
        'cost_center','direktorat','divisi','shortname'
    ];

    public function project(){
        return $this->hasMany(Project::class,'divisi_id');
    }

    public function user(){
        return $this->hasMany(User::class,'divisi');
    }
}
