<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'implementation_id'
    ];

    protected $with = ['user', 'project', 'implementation'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'personal_number');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function implementation()
    {
        return $this->belongsTo(Implementation::class, 'implementation_id');
    }
}
