<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson_learned extends Model
{
    protected $fillable = ['id','project_id','divisi_id','consultant_id','tahap','lesson_learned','detail', 'checker_at', 'signer_at', 'review_at', 'publish_at'];

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }

    public function divisi(){
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function consultant(){
        return $this->belongsTo(Consultant::class, 'consultant_id');
    }
}
