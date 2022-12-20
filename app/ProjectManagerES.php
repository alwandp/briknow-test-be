<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ProjectManagerES extends Model
{
    use Searchable;

    protected $fillable = [
        'nama','email'
    ];

    protected $table = 'project_managers';

    public function project(){
        return $this->hasMany(Project::class,'id');
    }

    protected $mapping = [
        'properties'    =>  [
            'id'=>[
                'index' =>  'integer',
                'type'  =>  'not_analyzed'
            ],
            'nama' => [
                'type' => 'string',
                'analyzer' => 'english'
            ],
            'email' => [
                'type' => 'string',
                'analyzer' => 'english'
            ],
        ]
    ];
}
