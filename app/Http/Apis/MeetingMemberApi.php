<?php

namespace App\Http\Apis;

use App\Models\Dto\ApiResponse;
use App\Services\MeetingService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class MeetingMemberApi  extends BaseController
{
    private $meetingService;

    function __construct()
    {
        $this->meetingService = new MeetingService();
    }

    public function add(){
        $json = request()->json()->all();
        $res = $this->meetingService->addMember($json['meeting_id'], $json['members']);

        return response()->json(new ApiResponse($res));
    }

    public function detail(){
        $json = request()->json()->all();
        $res = $this->meetingService->getMemberDetail($json['meeting_member_id']);

        return response()->json(new ApiResponse($res));
    }
}