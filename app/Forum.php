<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $fillable = [
        'user_id','slug','judul','content','kategori', 'status', 'restriction'
    ];

    protected $with = ['forumUser'];
    public function forumcomment(){
        return $this->hasMany(ForumComment::class,'forum_id');
    }

    public function user(){
        return $this->belongsTo(user::class,'user_id', 'personal_number');
    }

    public function forumUser(){
        return $this->hasMany(ForumUser::class,'forum_id','id');
    }
}
