<?php

namespace Songshenzong\Log;

use Illuminate\Database\Eloquent\Model;

class SongshenzongLog extends Model
{

    protected $fillable = [
        'data',
        'meta_utime',
        'meta_datetime',
        'meta_uri',
        'meta_ip',
        'meta_method',
    ];


    protected $casts = [
        'data' => 'array',
    ];

}
