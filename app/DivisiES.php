<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class DivisiES extends Model
{
    use Searchable;

    protected $fillable = [
        'cost_center','direktorat','divisi','shortname'
    ];

    protected $table = 'divisis';

    public function searchableAs()
    {
        return 'divisi';
    }

    public function project(){
        return $this->hasMany(Project::class,'divisi_id');
    }

    public function user(){
        return $this->hasMany(User::class,'divisi');
    }

    protected $mapping = [
        'properties'    =>  [
            'id'=>[
                'index' =>  'integer',
                'type'  =>  'not_analyzed'
            ],
            'cost_center' => [
                'type' => 'string',
                'analyzer' => 'not_analyzed'
            ],
            'direktorat' => [
                'type' => 'string',
                'analyzer' => 'english'
            ],
            'divisi' => [
                'type' => 'string',
                'analyzer' => 'english'
            ],'shortname' => [
                'type' => 'string',
                'analyzer' => 'english'
            ]
        ]
    ];

    public function toSearchableArray()
    {
        return array_merge(
            $this->toArray(),
            [ 
                'project' => $this->project,
            ]
        );
    }
}
