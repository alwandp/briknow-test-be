<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumUser extends Model
{
    protected $fillable = [
        'user_id','forum_id'
    ];

    protected $with     = [
        'user'
    ];

    public $timestamps = false;

    public function forum(){
        return $this->belongsTo(Forum::class,'forum_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','personal_number');
    }
}
