<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meeting';

    protected $primaryKey = 'meeting_id';

    // public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'description', 
        'location', 
        'start_scheduled', 
        'finish_scheduled', 
        'started_at', 
        'finished_at'
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
    protected $casts = [
        'start_scheduled' => 'datetime',
        'finish_scheduled' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
    
    public function members()
    {
        return $this->hasMany(MeetingMember::class, 'meeting_id', 'meeting_id');
    }
}
