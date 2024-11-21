<?php

namespace App\Http\Apis;

use App\Models\Dto\ApiResponse;
use App\Services\MasterService;
use App\Services\MeetingService;
use App\Services\RoomService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class MasterApi extends BaseController
{
    function __construct()
    {
    }

    public function list_room(RoomService $roomService){
        $res = $roomService->list();

        return response()->json(new ApiResponse($res));
    }
}
