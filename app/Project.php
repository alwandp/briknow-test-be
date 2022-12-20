<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Project extends Model
{
    protected $fillable = [
        'divisi_id', 'project_managers_id','thumbnail', 'nama', 'slug', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai', 'status_finish', 'metodologi','is_recomended','is_restricted', 'user_maker', 'user_checker', 'user_signer', 'flag_mcs','checker_at','signer_at','review_at','publish_at','status_read','r_note1','r_note2', 'flag_es'
    ];

    protected $table = 'projects';

    protected $dates = [
        'tanggal_mulai', 'tanggal_selesai'
    ];

    protected $with = [
        'project_managers','divisi','keywords', 'consultant','lesson_learned','comment','usermaker','userchecker','usersigner', 'communication_support', 'implementation', 'document'
    ];

    public function favorite_project(){
        return $this->hasMany(Favorite_project::class,'project_id');
    }

    public function keywords(){
        return $this->hasMany(Keywords::class,'project_id');
    }

    public function search_log(){
        return $this->hasMany(Search_log::class,'project_id');
    }

    public function project_managers(){
        return $this->belongsTo(Project_managers::class,'project_managers_id');
    }

    public function document(){
        return $this->hasMany(Document::class,'project_id');
    }

    public function consultant(){
        return $this->belongsToMany(Consultant::class,'consultant_projects','project_id','consultant_id');
    }

    public function divisi(){
        return $this->belongsTo(Divisi::class,'divisi_id');
    }

    public function user_restrict(){
        return $this->hasMany(Restriction::class,'project_id');
    }

    public function lesson_learned(){
        return $this->hasMany(Lesson_learned::class,'project_id');
    }

    public function comment(){
        return $this->hasMany(Comment::class,'project_id');
    }

    public function usermaker(){
        return $this->belongsTo(User::class,'user_maker','personal_number');
    }

    public function userchecker(){
        return $this->belongsTo(User::class,'user_checker','personal_number');
    }

    public function usersigner(){
        return $this->belongsTo(User::class,'user_signer','personal_number');
    }

    public function communication_support(){
        return $this->hasMany(CommunicationSupport::class,'project_id');
    }

    public function implementation(){
        return $this->hasMany(Implementation::class,'project_id');
    }
}
