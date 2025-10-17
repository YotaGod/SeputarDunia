<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\CategoryModel; 

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * DO NOT CHANGE THIS!
 */
abstract class BaseController extends Controller
{

    protected $categoryModel; // Deklarasikan model
    protected $data = [];     // Gunakan properti data untuk menampung data umum

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be automatically loaded upon
     * class instantiation. These helpers will be available
     * in all other controllers.
     *
     * @var list<string>
     */
    // protected $helpers = []; // DIHAPUS/DIKOMENTARI UNTUK MENGHINDARI CONFLICT TIPE DI VERSI PHP LAMA

    /**
     * Be sure to declare properties for any property initialization you do.
     * Only variables without initialized values should be declared here as
     * properties.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Inisialisasi Model di BaseController
        $this->categoryModel = new CategoryModel();
        
        // Muat data kategori dan simpan di properti $data
        $this->data['categories'] = $this->categoryModel->findAll();

        // Memuat helper yang dibutuhkan
        helper(['form', 'url', 'acl']); 
    }
}
