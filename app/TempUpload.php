<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempUpload extends Model
{
    protected $fillable = ['nama_file','path','jenis','size','type'];
}