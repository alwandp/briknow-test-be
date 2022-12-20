<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MyComsup extends Model{
    protected $table = 'my_comsup';
    protected $fillable = [
        'user_id', 'comsup_id'
    ];

    protected $with = [
        'user', 'communication_support'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function communication_support()
    {
        return $this->belongsTo(CommunicationSupport::class, 'comsup_id');
    }
}
