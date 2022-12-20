<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationSupport extends Model {
    protected $table = 'communication_support';

    protected $fillable = ['id',
        'project_id',
        'title',
        'slug',
        'type_file',
        'desc',
        'status',
        'tanggal_upload',
        'views',
        'downloads',
        'thumbnail',
        'user_maker',
        'updated_by',
        'approve_at',
        'approve_by',
        'publish_at',
        'publish_by',
        'unpublish_at',
        'unpublish_by',
        'reject_at',
        'reject_by',
        'deleted_at',
        'deleted_by'];

    protected $with = ['attach_file', 'project'];

    public function project(){
        return $this->belongsTo(Projects::class,'project_id');
    }

    public function attach_file() {
        return $this->hasMany(AttachFile::class, 'com_id');
    }

    public function favorite_com(){
        return $this->hasMany(MyComsup::class,'comsup_id');
    }

}
