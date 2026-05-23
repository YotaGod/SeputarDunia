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
        // Mengambil variabel lingkungan dari .env (atau gunakan hardcode fallback)
        $this->apiKey = getenv('GNEWS_API_KEY') ?: '622b9cd23e74fb9a7c0430169c6f85c9';
        $this->baseUrl = 'https://gnews.io/api/v4/';
        
        $this->client = Services::curlrequest([
            'baseURI' => $this->baseUrl,
            'timeout' => 5,
            'headers' => [
                'User-Agent' => 'SeputarDunia-CI4-App/1.0', 
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Mengambil berita dari endpoint 'search' GNews API.
     * @param string $q Kata kunci pencarian.
     * @param int $pageSize Jumlah artikel per halaman.
     * @return array Hasil respons API.
     */
    public function fetchEverything(string $q = 'teknologi', int $pageSize = 5): array
    {
        try {
            // Panggilan API ke endpoint 'search' GNews
            $response = $this->client->get('search', [
                'query' => [
                    'apikey' => $this->apiKey,
                    'lang' => 'id', // GNews menggunakan parameter lang
                    'q' => $q,
                    'max' => $pageSize, // GNews menggunakan max untuk batasan
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();
            
            if ($statusCode === 200) {
                $result = json_decode($responseBody, true);
                
                // GNews mengembalikan 'articles'. Kita petakan 'image' ke 'urlToImage'
                // agar kompatibel dengan view lama (Home/index.php)
                if (isset($result['articles']) && is_array($result['articles'])) {
                    foreach ($result['articles'] as &$article) {
                        $article['urlToImage'] = $article['image'] ?? null;
                    }
                }
                
                // Tambahkan status 'ok' ala NewsAPI agar kompatibel dengan Home.php
                $result['status'] = 'ok';
                return $result;
            }

            $result = json_decode($responseBody, true);
            $apiErrorMsg = isset($result['errors']) ? implode(', ', $result['errors']) : 'Tidak ada pesan error dari API.';

            return [
                'status' => 'error', 
                'message' => 'Gagal mengambil data dari API. Status: ' . $statusCode . '. Respon API: ' . $apiErrorMsg
            ];

        } catch (\Exception $e) {
            log_message('error', 'GNews API Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}