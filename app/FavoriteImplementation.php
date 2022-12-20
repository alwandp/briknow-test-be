<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteImplementation extends Model{
    protected $table = 'favorite_implementation';
    protected $fillable = [
        'user_id', 'imp_id'
    ];

    protected $with = [
        'user', 'implementation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function implementation()
    {
        return $this->belongsTo(Implementation::class, 'imp_id');
    }
}
