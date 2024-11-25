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

    public function update(){
        $json = request()->json()->all();

        $this->meetingService->updateMember($json);

        return response()->json(new ApiResponse());
    }

    public function delete(){
        $json = request()->json()->all();

        $json['is_deleted'] = 1;
        $this->meetingService->updateMember($json);

        return response()->json(new ApiResponse());
    }

    public function detail($meeting_member_id){
        $json = request()->json()->all();
        $res = $this->meetingService->getMemberDetail($meeting_member_id);

        return response()->json(new ApiResponse($res));
    }
}
