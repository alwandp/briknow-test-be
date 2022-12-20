<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsultantLog extends Model
{
    protected $fillable = ['consultant_id','user_id'];
    
    protected $with = ['consultant'];

    public function consultant(){
        return $this->belongsTo(Consultant::class,'consultant_id');
    }
}
