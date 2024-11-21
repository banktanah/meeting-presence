<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meeting';

    protected $primaryKey = 'meeting_id';

    public $incrementing = false;
    protected $keyType = 'string';

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
        'scheduled_start', 
        'scheduled_finish', 
        'started_at', 
        'finished_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['code'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_finish' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
    
    public function members()
    {
        return $this->hasMany(MeetingMember::class, 'meeting_id', 'meeting_id');
    }
}
