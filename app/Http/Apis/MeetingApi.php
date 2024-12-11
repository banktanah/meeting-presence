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
        $json = request()->json()->all();

        if(empty($json['digital_signature']) && empty($json['photo'])){
            return response()->json(new ApiResponse(1, 'Tandatangan atau Foto harus diisi'));
        }

        if(!empty($json['face_encoding'])){
            $nip = null;
            if(!empty($json['meeting_member_id'])){
                $member = $this->meetingService->getMemberDetail($json['meeting_member_id']);
                if(empty($member->id_number)){
                    throw new \Exception("The user does not have an \"id_number\" while trying to save \"face_encoding\"", 1);
                }
                $nip = $member->id_number;
            }else if(!empty($json['nip'])){
                $nip = $json['nip'];
            }else{
                throw new \Exception("\"meeting_member_id\" or \"nip\" must be set to save \"face_encoding\"", 1);
            }

            $this->enroll_face($nip, 'NIP', $json['face_encoding']);
        }

        $this->meetingService->attend($json);

        return response()->json(new ApiResponse());
    }

    public function register_face(){
        $json = request()->json()->all();

        $this->enroll_face($json['person_id'], $json['id_type'], $json['encoding']);

        return response()->json(new ApiResponse());
    }

    private function enroll_face($person_id, $id_type, $encoding){
        $endpoint = config('app.api_endpoint.biometric');
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            "$endpoint/api/face/enroll.php",
            [
                'json' => [
                    'person_id' => $person_id,
                    'id_type' => $id_type,
                    'encoding' => $encoding
                ]
            ],
            ['Content-Type' => 'application/json']
        );

        $responseJSON = json_decode($response->getBody());

        return $responseJSON;
    }

    public function get_faces(){
        $json = request()->json()->all();

        $ids = [];
        if(!empty($json) && !empty($json['meeting_id'])){
            $res = $this->meetingService->listMember($json['meeting_id']);
    
            foreach($res as $row){
                if(!empty($row->id_number)){
                    $ids []= $row->id_number;
                }
            }
        }
        
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

        return response()->json(new ApiResponse($result));
    }

    public function get_all_faces(){
        $endpoint = config('app.api_endpoint.biometric');
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            "$endpoint/api/face/list.php",
            [
                'json' => [
                    'person_ids' => []
                ]
            ],
            ['Content-Type' => 'application/json']
        );

        $jsonResult = json_decode($response->getBody());
        $result = $jsonResult->data;

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
