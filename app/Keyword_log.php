<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword_log extends Model
{
    protected $fillable = [
        'user_id', 'nama'
    ];

    protected $mapping = [
        'properties'    =>  [
            'nama' => [                
                'index' =>  'string',
                'type'  =>  'not_analyzed'
            ],
            'user_id' => [                
                'index' =>  'integer',
                'type'  =>  'not_analyzed'
            ]
        ]
    ];
}
