<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalParticipant extends Model
{
    protected $table = 'external_participants';

    protected $primaryKey = 'external_participant_id';

    protected $fillable = [
        'name',
        'normalized_name',
        'instansi',
        'jabatan',
        'phone',
        'email',
        'last_seen_at',
        'is_deleted',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];
}
