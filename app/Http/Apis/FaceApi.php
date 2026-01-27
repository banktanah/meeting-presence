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
        try {
            // Fetch pegawai list from dashboard
            $dashboardEndpoint = config('app.api_endpoint.dashboard');
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $response = $client->get(
                "$dashboardEndpoint/dashboard/services/apps/mawas/listpegawai",
                ['Content-Type' => 'application/json']
            );

            $pegawaiList = json_decode($response->getBody());

            // Fetch face list from biometric
            $biometricEndpoint = config('app.api_endpoint.biometric');
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $response = $client->post(
                "$biometricEndpoint/api/face/list.php",
                [
                    'json' => [
                        'person_ids' => []
                    ]
                ],
                ['Content-Type' => 'application/json']
            );

            $jsonResult = json_decode($response->getBody());
            $faceList = $jsonResult->data ?? [];
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
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            return response()->json(['error' => 'Tidak dapat terhubung ke server: ' . $e->getMessage()], 503);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            return response()->json(['error' => 'Request gagal: ' . $e->getMessage()], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
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
    
    public function listPegawaiMawas(){
        try {
            $endpoint = config('app.api_endpoint.dashboard');
            $url = "$endpoint/dashboard/services/apps/mawas/listpegawai";
            
            // Debug: log the endpoint being used
            \Log::info('Fetching from dashboard', ['endpoint' => $endpoint, 'url' => $url]);
            
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $response = $client->get($url, ['Content-Type' => 'application/json']);

            $pegawaiList = json_decode($response->getBody(), true);
            return response()->json($pegawaiList);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            return response()->json([
                'error' => 'Tidak dapat terhubung ke server dashboard',
                'detail' => $e->getMessage()
            ], 503);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : null;
            return response()->json([
                'error' => 'Request ke dashboard gagal',
                'status' => $statusCode,
                'detail' => $e->getMessage(),
                'response' => $responseBody
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}
