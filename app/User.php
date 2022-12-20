<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','username', 'personal_number', 'role','last_login_at','divisi', 'xp','avatar_id'
    ];

    protected $dates= [
        'last_login_at'
    ];

    protected $with = [
        // 'divisi','divisis', 'achievement','activity','userlevel'
        'divisis', 'achievement','activity','userlevel'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_log(){
        return $this->HasMany(User_log::class,'user_id');
    }

    public function search_log(){
        return $this->HasMany(Search_log::class,'user_id');
    }

    public function divisi(){
        return $this->hasOne(Divisi::class,'divisi');
    }

    public function divisis(){
        return $this->belongsto(Divisi::class,'divisi');
    }

    public function favorite_consultant(){
        return $this->HasMany(Favorite_consultant::class,'user_id');
    }

    public function favorite_project(){
        return $this->hasMany(Favorite_project::class,'user_id');
    }

    public function favorite_com(){
        return $this->hasMany(MyComsup::class,'user_id');
    }

    public function favorite_implementation(){
        return $this->hasMany(FavoriteImplementation::class,'user_id');
    }

    public function achievement(){
        return $this->hasMany(UserAchievement::class,'personal_number','personal_number');
    }

    public function activity(){
        return $this->hasMany(ActivityUser::class,'personal_number','personal_number');
    }

    public function userlevel(){
        return $this->hasMany(UserLevel::class,'personal_number','personal_number');
    }
}