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

    public function register_face(){
        $json = request()->json()->all();

        $endpoint = config('app.api_endpoint.biometric');
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            "$endpoint/api/face/enroll.php",
            [
                'json' => [
                    'person_id' => $json['person_id'],
                    'id_type' => $json['id_type'],
                    'encoding' => $json['encoding']
                ]
            ],
            ['Content-Type' => 'application/json']
        );

        $responseJSON = json_decode($response->getBody(), true);

        return response()->json(new ApiResponse());
    }

    public function get_faces(string $meeting_id_or_code){
        $res = $this->meetingService->listMember($meeting_id_or_code);

        $ids = [];
        foreach($res as $row){
            if(!empty($row->id_number)){
                $ids []= $row->id_number;
            }
        }
        
        $result = null;
        if(!empty($ids)){
            $endpoint = config('app.api_endpoint.biometric');
            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                "$endpoint/api/face/list.php",
                [
                    'json' => [
                        'person_ids' => $ids
                    ]
                ],
                ['Content-Type' => 'application/json']
            );

            $jsonResult = json_decode($response->getBody());
            $result = $jsonResult->data;
        }

        return response()->json(new ApiResponse($result));
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
