<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keywords_document extends Model
{
    protected $fillable = [
        'document_id', 'name'
    ];

    public function document(){
        return $this->belongsTo(Document::class,'document_id');
    }
}
