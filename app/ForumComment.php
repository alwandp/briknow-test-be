<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $fillable = [
        'forum_id','parent_id','user_id','replyto_user_id','comment'
    ];

    protected $with = [
        'child','user','reply_user',
    ];

    public function forum(){
        return $this->belongsTo(Forum::class,'forum_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','personal_number');
    }

    public function Reply_user(){
        return $this->belongsTo(User::class,'replyto_user_id','personal_number');
    }

    public function parent(){
        return $this->belongsTo(ForumComment::class,'parent_id');
    }

    public function child(){
        return $this->hasMany(ForumComment::class,'parent_id');
    }
}
