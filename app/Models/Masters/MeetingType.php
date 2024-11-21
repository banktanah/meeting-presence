<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class MeetingType extends Model
{
    protected $table = 'master_meeting_type';

    protected $primaryKey = 'meeting_type_id';

    // public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
