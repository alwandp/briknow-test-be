<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class KeywordES extends Model
{
    use Searchable;

    protected $fillable = [
        'project_id', 'nama'
    ];

    protected $table = 'keywords';

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }

    public function keyword_log(){
        return $this->hasMany(Keyword_log::class,'nama','nama');
    }

    protected $mapping = [
        'properties'    =>  [
            'id'=>[
                'index' =>  'integer',
                'type'  =>  'not_analyzed'
            ],
            'project_id' => [                
                'index' =>  'integer',
                'type'  =>  'not_analyzed'
            ],
            'nama' => [
                'type' => 'text',
                'analyzer' => 'whitespace'
            ]
        ]
    ];
}
