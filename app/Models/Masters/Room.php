<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'master_room';

    protected $primaryKey = 'room_id';

    public $incrementing = false;
    protected $keyType = 'string';

    // public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_id', 
        'name', 
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];
}
