<?php

namespace App\Http\Controllers;

use App\Services\MeetingService;

class MeetingController extends _BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index(MeetingService $meetingService)
    {
        return view('pages/meeting/list');
    }

    public function meeting_presence(string $meeting_id)
    {
        return view('pages/meeting/presence', ['meeting_id' => $meeting_id]);
    }
}
