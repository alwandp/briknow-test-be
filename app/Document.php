<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'project_id', 'nama', 'jenis_file','url_file', 'size'
    ];

    public function keywords_document(){
        return $this->hasMany(Keywords_document::class,'id');
    }

    public function project(){
        return $this->belongsTo(Project::class,'id');
    }
}
