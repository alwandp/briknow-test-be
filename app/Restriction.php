<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    protected $fillable = [
        'user_id', 'project_id'
    ];

    protected $with = ['user', 'project'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'personal_number');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
