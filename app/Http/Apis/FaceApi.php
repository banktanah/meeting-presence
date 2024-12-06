<?php

namespace App\Http\Apis;

use App\Models\Dto\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

class FaceApi extends BaseController
{
    function __construct()
    {
    }

    public function listPegawai(){
        $endpoint = config('app.api_endpoint.dashboard');
        $client = new \GuzzleHttp\Client();
        $response = $client->get(
            "$endpoint/services/apps/mawas/listpegawai",
            ['Content-Type' => 'application/json']
        );

        $pegawaiList = json_decode($response->getBody());

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
        $faceList = $jsonResult->data;
        $faceExistIds = [];
        foreach($faceList as $row){
            $faceExistIds []= $row->person_id;
        }

        $result = [];
        foreach($pegawaiList as $row){
            $result []= [
                'nip' => $row->nip,
                'nama' => $row->nama,
                'face_registered' => in_array($row->nip, $faceExistIds)
            ];
        }

        return response()->json(new ApiResponse($result));
    }

    public function get_base64_photos(){
        $json = request()->json()->all();

        $endpoint = config('app.api_endpoint.esdm');
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            "$endpoint/Karyawan/get_photos_base64",
            [
                'json' => [
                    'nips' => $json['ids']
                ]
            ],
            ['Content-Type' => 'application/json']
        );

        $jsonResult = json_decode($response->getBody());

        return response()->json(new ApiResponse($jsonResult->data));
    }
}
