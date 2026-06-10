<?php

namespace App\Http\Apis;

use App\Models\Dto\ApiResponse;
use App\Services\MeetingService;
use Illuminate\Routing\Controller as BaseController;

class ExternalParticipantApi extends BaseController
{
    private $meetingService;

    public function __construct()
    {
        $this->meetingService = new MeetingService();
    }

    public function search()
    {
        $keyword = request()->query('keyword', '');
        $res = $this->meetingService->searchExternalParticipants($keyword);

        return response()->json(new ApiResponse($res));
    }
}
