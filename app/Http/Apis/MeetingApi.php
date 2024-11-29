<?php

namespace App\Http\Apis;

use App\Exceptions\ApiException;
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

    public function get(string $meeting_id_or_code){
        $res = $this->meetingService->get($meeting_id_or_code);

        return response()->json(new ApiResponse($res));
    }

    public function members(string $meeting_id_or_code){
        $res = $this->meetingService->listMember($meeting_id_or_code);

        return response()->json(new ApiResponse($res));
    }

    public function presence(){
        $params = request()->input();
        $json = request()->json()->all();

        $this->meetingService->attend($json);

        return response()->json(new ApiResponse());
    }

    public function add(){
        $json = request()->json()->all();

        $this->meetingService->add($json);

        return response()->json(new ApiResponse());
    }

    public function update(){
        $json = request()->json()->all();

        $this->meetingService->update($json);

        return response()->json(new ApiResponse());
    }

    public function add_document(){
        $json = request()->json()->all();

        $this->meetingService->addDocument($json);

        return response()->json(new ApiResponse());
    }

    public function delete(){
        $json = request()->json()->all();

        $json['is_deleted'] = 1;
        $this->meetingService->update($json);

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
