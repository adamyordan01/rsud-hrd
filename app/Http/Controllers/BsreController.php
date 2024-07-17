<?php

namespace App\Http\Controllers;

use CURLFile;
use GuzzleHttp\Client;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BsreController extends Controller
{
    private $http;
    private $timeout;

    public function __construct($timeout = 480)
    {
        $this->http = new Client();
        $this->timeout = $timeout;

        // Set default timezone to Asia/Jakarta
        date_default_timezone_set('Asia/Jakarta');
    }

    public function checkStatus(Request $request)
    {
        $karyawan = $request->kd_karyawan;
        $urut = $request->urut_rincian_verif;
        $tahun = $request->tahun_rincian_verif;

        $client = new Client();
        $url = "http://123.108.100.83:85/api/user/status/1271022205700001";

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Basic ZXNpZ246cXdlcnR5',
                    'Accept' => 'application/json',
                ],
                'timeout' => 60,
            ]);

            if ($response->getStatusCode() == 200) {
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Server is up and running',
                ]);
            }
        } catch (\Exception $e) {
            DB::table('hrd_sk_pegawai_kontrak')
                ->where('kd_karyawan', $karyawan)
                ->where('urut', $urut)
                ->where('tahun_sk', $tahun)
                ->update([
                    'verif_4' => 0,
                    'kd_karyawan_verif_4' => null,
                    'tgl_ttd' => null,
                    'no_sk' => null,
            ]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function signPdf(Request $request)
    {
        $file = storage_path('app/public/sk/SK Pegawai Kontrak-2024-001512.pdf');

        $endpoint = "http://123.108.100.83:85/api/sign/pdf";
        $filename = basename($file);
        $dataFile = file_get_contents($file);

        try {
            $response = $this->http->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $dataFile,
                        'filename' => $filename,
                    ],
                    [
                        'name' => 'nik',
                        'contents' => '1271022205700001'
                    ],
                    [
                        'name' => 'passphrase',
                        'contents' => 'Helmiza29#'
                    ],
                    [
                        'name' => 'tampilan',
                        'contents' => 'invisible'
                    ],
                ],
                'timeout' => $this->timeout,
            ]);

            // get response from headers
            $headers = $response->getHeaders();
            
            // then get id_dokumen from headers
            $id = $headers['id_dokumen'];
            

            // "code": 200,
            // "status": "success",
            // "message": "PDF berhasil ditandatangani",
            // "id": [
            //     "897848f00a75488eba468f9baed7ffc5"
            // ]

            // jadikan id sebagai array
            $id = $id[0];

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'PDF berhasil ditandatangani',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            Log::error('Error signing PDF: ' . $e->getMessage());

            // Client error: `POST http://123.108.100.83:85/api/sign/pdf` resulted in a `400 Bad Request` response:\n{\"error\":\"Proses signing gagal : User tidak terdaftar 2011\",\"status_code\":400}\n
            // ambil pesan error dari response beserta status code
            $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);

            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => $responseBody['error'],
            ]);
        }
    }

    public function old_signPdf_1(Request $request)
    {
        $endpoint = "http://123.108.100.83:85/api/sign/pdf";
        
        $file = storage_path('app/public/sk/SK Pegawai Kontrak-2024-001512.pdf');

        $dirname = dirname($file);
        $filename = basename($file);

        $filePdf = new CURLFile($file, 'application/pdf');

        $postFields = [
            'file' => $filePdf,
            'nik' => '1271022205700001',
            'passphrase' => 'Helmiza29#',
            'tampilan' => 'invisible'
        ];

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic ZXNpZ246cXdlcnR5"
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        curl_setopt($ch, CURLOPT_TIMEOUT, 480);
        $response = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $responseBody = json_decode($response, true);
        
        // check response
        // dd($ch, $httpcode, $responseBody);

    }

    public function old_signPdf(Request $request)
    {
        // ambil file di C:\laragon\www\rsud_hrd\storage\app\public\sk\SK Pegawai Kontrak-2024-001512.pdf
        $file = storage_path('app/public/sk/SK Pegawai Kontrak-2024-001512.pdf');
        
        $endpoint = "http://123.108.100.83:85/api/sign/pdf";
        $passphrase = 'Helmiza29#';

        $curlFile = new \CURLFile($file, 'application/pdf', basename($file));
        $postFields = [
            'file' => $curlFile,
            'nik' => '1271022205700001',
            'passphrase' => $passphrase,
            'tampilan' => 'invisible'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic ZXNpZ246cXdlcnR5"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 480);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error('cURL error: ' . $error);

            return response()->json([
                'code' => 500,
                'status' => 'gagal',
                'message' => 'cURL error: ' . $error,
                'type' => 2
            ]);
        }
        
        curl_close($ch);
        
        // Log raw response
        Log::info('Raw response from e-signature API: ' . $response);

        $responseBody = json_decode($response, true);

        if (!is_array($responseBody)) {
            Log::error('Invalid JSON response from e-signature API');

            return response()->json([
                'code' => 500,
                'status' => 'gagal',
                'message' => 'Invalid JSON response from e-signature API',
                'type' => 2
            ]);
        }

        if (isset($responseBody["status_code"]) && ($responseBody["status_code"] == 400 || $responseBody["status_code"] == 500)) {
            Log::error('Error signing PDF: ' . $responseBody['error']);
            
            return response()->json([
                'code' => 500,
                'status' => 'gagal',
                'message' => $responseBody['error'],
                'type' => 1
            ]);
        } else {
            // Parse headers for id_dokumen
            $headers = $httpCode;
            $id = $this->extractIdFromHeaders($headers);

            Log::info('PDF signed successfully: ' . $id);

            return response()->json([
                'code' => 200,
                'status' => 'sukses',
                'message' => 'PDF berhasil ditandatangani',
                'id' => $id
            ]);
        }
    }
}