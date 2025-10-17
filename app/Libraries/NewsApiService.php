<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class NewsApiService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        // Mengambil variabel lingkungan dari .env
        $this->apiKey = '39313a911c124c21a759cbc1eb88b219'; // KUNCI API ANDA
        $this->baseUrl = 'https://newsapi.org/v2/';
        
        // Menggunakan CI4 HTTP Client
        $this->client = Services::curlrequest([
            'baseURI' => $this->baseUrl,
            'timeout' => 5,
            
            // SOLUSI KRITIS: Menambahkan Header User-Agent (Wajib untuk News API)
            'headers' => [
                // Ganti dengan nama aplikasi Anda untuk identifikasi
                'User-Agent' => 'SeputarDunia-CI4-App/1.0 (contact@seputardunia.com)', 
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Mengambil berita dari endpoint 'everything' News API.
     * Digunakan untuk menggantikan fetchTopHeadlines jika ada masalah region/topik.
     * @param string $q Kata kunci pencarian.
     * @param int $pageSize Jumlah artikel per halaman.
     * @return array Hasil respons API.
     */
    public function fetchEverything(string $q = 'teknologi', int $pageSize = 5): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new \Exception('NEWSAPI_KEY tidak ditemukan di .env');
            }

            // Panggilan API ke endpoint 'everything'
            $response = $this->client->get('everything', [
                'query' => [
                    'apiKey' => $this->apiKey,
                    'language' => 'en', // Menggunakan bahasa Inggris untuk hasil yang lebih pasti
                    'q' => $q,
                    'pageSize' => $pageSize,
                ],
                // Header sudah di-set di __construct
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();
            
            if ($statusCode === 200) {
                return json_decode($responseBody, true);
            }

            $result = json_decode($responseBody, true);
            $apiErrorMsg = isset($result['message']) ? $result['message'] : 'Tidak ada pesan error dari API.';

            return [
                'status' => 'error', 
                'message' => 'Gagal mengambil data dari API. Status: ' . $statusCode . '. Respon API: ' . $apiErrorMsg
            ];

        } catch (\Exception $e) {
            log_message('error', 'News API Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}