<?php

namespace Songshenzong\Log;

use Illuminate\Database\Eloquent\Model;


class SongshenzongLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data',
        'utime',
        'datetime',
        'uri',
        'ip',
        'method',
    ];

    protected $casts = [
        'data' => 'array',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];


}
