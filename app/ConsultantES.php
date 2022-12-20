<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ConsultantES extends Model
{
    use Searchable;

    protected $fillable = [
        'nama', 'website', 'telepon', 'email', 'facebook','instagram','lokasi','tentang','bidang'
    ];

    protected $table = 'consultants';
    
    public function searchableAs()
    {
        return 'consultant';
    }

    public function favorite_consultant(){
        return $this->hasMany(Favorite_consultant::class,'consultant_id');
    }

    public function project(){
        return $this->belongsToMany(Project::class,'consultant_projects','consultant_id','project_id');
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
            'bidang' => [
                'type' => 'string',
                'analyzer' => 'english'
            ],
            'tentang' => [
                'type' => 'text',
                'analyzer' => 'english'
            ],
            'website' => [
                'type' => 'string',
                'analyzer' => 'english'
            ],
            'telepon' => [
                'type' => 'text',
                'analyzer' => 'english'
            ],
            'email' => [
                'type' => 'text',
                'analyzer' => 'english'
            ],
            'facebook' => [
                'type' => 'text',
                'analyzer' => 'english'
            ],
            'instagram' => [
                'type' => 'text',
                'analyzer' => 'english'
            ],
            'lokasi' => [
                'type' => 'text',
                'analyzer' => 'english'
            ],
        ]
    ];

    public function toSearchableArray()
    {
        return array_merge(
            // By default all model fields will be indexed
            $this->toArray(),
            [ 
                'project' => $this->project,
                'favorite_consultant' => $this->favorite_consultant,
            ]
        );
    }
}
