<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsultantProject extends Model
{
    protected $fillable = ['consultant_id','project_id'];

    protected $with     = ['consultant'];

    public $timestamps = false;

    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'consultant_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}