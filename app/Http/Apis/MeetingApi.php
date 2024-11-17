<?php

namespace App\Http\Apis;

use App\Models\Dto\ApiResponse;
use App\Services\MeetingService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class MeetingApi extends BaseController
{
    private $meetingService;

    function __construct()
    {
        $this->meetingService = new MeetingService();
    }

    public function list(){
        $res = $this->meetingService->list();

        return response()->json(new ApiResponse($res));
    }

    public function get(string $meeting_id){
        $res = $this->meetingService->get($meeting_id);

        return response()->json(new ApiResponse($res));
    }

    public function members(string $meeting_id){
        $res = $this->meetingService->listMember($meeting_id);

        return response()->json(new ApiResponse($res));
    }

    public function presence(){
        $params = request()->input();

        $this->meetingService->attend($params['meeting_member_id'], $params['signature']);

        return response()->json(new ApiResponse());
    }

    public function get_file(string $code){
        $tmp = base64_decode($code);
        $fileinfo = json_decode(base64_decode($code));

        return response()->download(
            storage_path("app/laporan/$fileinfo->laporan_id/$fileinfo->filename"), 
            $fileinfo->filename, 
            []
        );
    }
}
