<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $fillable = ['user_id','kategori','judul','pesan','status','direct'];
    protected $with     = ['User'];

    public function User(){
        return $this->belongsTo(User::class,'user_id','personal_number');
    }
}