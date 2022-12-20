<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Project_managers extends Model
{
    protected $fillable = [
        'nama','email'
    ];

    public function project(){
        return $this->hasMany(Project::class,'id');
    }
}
