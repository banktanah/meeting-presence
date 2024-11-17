<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingMember extends Model
{
    protected $table = 'meeting_member';

    protected $primaryKey = 'meeting_member_id';

    // public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'meeting_id',
        'id_number',
        'name', 
        'role',
        'description', 
        'is_attend', 
        'attend_at', 
        'digital_signature'
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
        'attend_at' => 'datetime',
    ];
}
