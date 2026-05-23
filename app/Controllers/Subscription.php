<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\UserModel;

class Subscription extends BaseController
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = getenv('MIDTRANS_SERVER_KEY');
        Config::$clientKey = getenv('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = filter_var(getenv('MIDTRANS_IS_PRODUCTION'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk berlangganan.');
        }

        return view('frontend/subscription/index');
    }

    public function pay()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not logged in']);
        }

        $userId = session()->get('user_id');
        $username = session()->get('username');
        $email = session()->get('email');
        
        $json = $this->request->getJSON();
        $duration = isset($json->duration) ? (int)$json->duration : 1;
        
        if (!in_array($duration, [1, 3, 12])) {
            $duration = 1;
        }

        if ($duration == 12) {
            $grossAmount = 500000;
        } elseif ($duration == 3) {
            $grossAmount = 140000;
        } else {
            $grossAmount = 50000;
        }

        // Format Order ID: SUB-{user_id}-{duration}-{timestamp}
        $orderId = 'SUB-' . $userId . '-' . $duration . '-' . time();

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ),
            'customer_details' => array(
                'first_name' => $username,
                'email' => $email,
            ),
        );

        try {
            $snapToken = Snap::getSnapToken($params);
            return $this->response->setJSON(['status' => 'success', 'snapToken' => $snapToken, 'order_id' => $orderId]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function notification()
    {
        try {
            $notif = new \Midtrans\Notification();

            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            
            // Extract user_id and duration from orderId (format: SUB-{user_id}-{duration}-{timestamp})
            $parts = explode('-', $orderId);
            $userId = isset($parts[1]) ? $parts[1] : null;
            $duration = isset($parts[2]) ? (int)$parts[2] : 1;

            if ($userId) {
                $userModel = new UserModel();
                $db = \Config\Database::connect();
                $subBuilder = $db->table('subscriptions');
                
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    // Update user role to Pengunjung Berlangganan (role_id 4)
                    $userModel->update($userId, ['role_id' => 4]);

                    // Check existing subscription
                    $existingSub = $subBuilder->where('user_id', $userId)->get()->getRow();

                    if ($existingSub && $existingSub->status === 'active' && strtotime($existingSub->end_date) > time()) {
                        // Perpanjang masa aktif yang ada
                        $currentEnd = new \DateTime($existingSub->end_date);
                        $currentEnd->modify('+' . $duration . ' months');
                        
                        $subBuilder->where('user_id', $userId)->update([
                            'end_date' => $currentEnd->format('Y-m-d H:i:s')
                        ]);
                    } else {
                        // Buat langganan baru atau perbarui langganan kedaluwarsa
                        $startDate = new \DateTime();
                        $endDate = clone $startDate;
                        $endDate->modify('+' . $duration . ' months');

                        if ($existingSub) {
                            $subBuilder->where('user_id', $userId)->update([
                                'start_date' => $startDate->format('Y-m-d H:i:s'),
                                'end_date' => $endDate->format('Y-m-d H:i:s'),
                                'status' => 'active',
                                'payment_method' => $notif->payment_type
                            ]);
                        } else {
                            $subBuilder->insert([
                                'user_id' => $userId,
                                'start_date' => $startDate->format('Y-m-d H:i:s'),
                                'end_date' => $endDate->format('Y-m-d H:i:s'),
                                'status' => 'active',
                                'payment_method' => $notif->payment_type
                            ]);
                        }
                    }
                }
            }

            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            log_message('error', 'Midtrans Notification Error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function finishLocal()
    {
        $json = $this->request->getJSON();
        $orderId = isset($json->order_id) ? $json->order_id : null;

        if ($orderId) {
            $parts = explode('-', $orderId);
            $userId = isset($parts[1]) ? $parts[1] : null;
            $duration = isset($parts[2]) ? (int)$parts[2] : 1;

            if ($userId) {
                $userModel = new UserModel();
                $db = \Config\Database::connect();
                $subBuilder = $db->table('subscriptions');
                
                $userModel->update($userId, ['role_id' => 4]);
                $existingSub = $subBuilder->where('user_id', $userId)->get()->getRow();

                if ($existingSub && $existingSub->status === 'active' && strtotime($existingSub->end_date) > time()) {
                    $currentEnd = new \DateTime($existingSub->end_date);
                    $currentEnd->modify('+' . $duration . ' months');
                    $subBuilder->where('user_id', $userId)->update([
                        'end_date' => $currentEnd->format('Y-m-d H:i:s')
                    ]);
                } else {
                    $startDate = new \DateTime();
                    $endDate = clone $startDate;
                    $endDate->modify('+' . $duration . ' months');

                    if ($existingSub) {
                        $subBuilder->where('user_id', $userId)->update([
                            'start_date' => $startDate->format('Y-m-d H:i:s'),
                            'end_date' => $endDate->format('Y-m-d H:i:s'),
                            'status' => 'active',
                            'payment_method' => 'bank_transfer'
                        ]);
                    } else {
                        $subBuilder->insert([
                            'user_id' => $userId,
                            'start_date' => $startDate->format('Y-m-d H:i:s'),
                            'end_date' => $endDate->format('Y-m-d H:i:s'),
                            'status' => 'active',
                            'payment_method' => 'bank_transfer'
                        ]);
                    }
                }
                return $this->response->setJSON(['status' => 'success']);
            }
        }
        return $this->response->setJSON(['status' => 'error']);
    }
}
