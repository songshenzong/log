<?php

namespace Songshenzong\Log;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SongshenzongLog
 *
 * @package Songshenzong\Log
 */
class SongshenzongLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time',
        'ip',
        'method',
        'uri',
        'data',
    ];

    /**
     * @var array
     */
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


    /**
     * @param $key
     *
     * @return false|string
     */
    public function getTimeAttribute($key)
    {
        return date('m/d/y H:i:s:ms', $key);
    }
}
