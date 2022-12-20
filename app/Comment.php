<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'parent_id','user_id','project_id','replyto_user_id','comment'
    ];

    protected $with = [
        'child','user','reply_user'
    ];

    public function parent(){
        return $this->belongsTo(Comment::class,'parent_id');
    }

    public function child(){
        return $this->hasMany(Comment::class,'parent_id');
    }

    public function User(){
        return $this->belongsTo(User::class,'user_id','personal_number');
    }

    public function Reply_user(){
        return $this->belongsTo(User::class,'replyto_user_id','personal_number');
    }

    public function Project(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
