<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berlangganan | Seputar Dunia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg-color: #0b0f19;
            --glass-bg: rgba(11, 15, 25, 0.75);
            --border-color: rgba(255, 255, 255, 0.1);
            --accent-color: #3b82f6; 
            --accent-hover: #60a5fa;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
        }
        .btn-primary-custom {
            background-color: var(--accent-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }
        .btn-primary-custom:hover {
            background-color: var(--accent-hover);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <a href="<?= base_url() ?>" class="btn btn-outline-secondary" style="border-radius: 8px;">
        <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
    </a>
</div><style>
    .subscription-card {
        background: var(--glass-bg);
        border: 1px solid var(--accent-color);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        padding: 3rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
        max-width: 500px;
        margin: 0 auto;
    }
    .price-tag {
        font-size: 3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 1.5rem 0;
    }
    .price-currency {
        font-size: 1.5rem;
        vertical-align: super;
    }
    .price-period {
        font-size: 1rem;
        color: var(--text-secondary);
        font-weight: 400;
    }
    .feature-list {
        text-align: left;
        margin: 2rem 0;
        list-style: none;
        padding: 0;
    }
    .feature-list li {
        margin-bottom: 1rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .feature-list li i {
        color: var(--accent-color);
    }
</style>

<?php
$daysLeft = 0;
if (session()->get('isLoggedIn')) {
    $db = \Config\Database::connect();
    $sub = $db->table('subscriptions')->where('user_id', session()->get('user_id'))->where('status', 'active')->get()->getRow();
    if ($sub && strtotime($sub->end_date) > time()) {
        $daysLeft = ceil((strtotime($sub->end_date) - time()) / 86400);
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center align-items-stretch">
        
        <?php if ($daysLeft > 0): ?>
        <div class="col-md-5 mb-4 mb-md-0">
            <div class="subscription-card h-100 d-flex flex-column justify-content-center" style="max-width: 100%;">
                <h3 class="fw-bold text-warning mb-3"><i class="fas fa-clock"></i> Sisa Waktu Anda</h3>
                <div class="display-1 fw-bold text-primary mb-2"><?= $daysLeft ?></div>
                <p class="text-secondary fs-5">Hari</p>
                <p class="text-muted small mt-4 px-3">Jangan khawatir, perpanjangan akan secara otomatis ditambahkan ke sisa hari Anda tanpa mereset waktu yang ada.</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?= $daysLeft > 0 ? 'col-md-7' : 'col-md-12' ?>">
            <div class="subscription-card h-100" <?= $daysLeft > 0 ? 'style="max-width: 100%;"' : '' ?>>
                <h2 class="fw-bold mb-3">Akses Premium</h2>
                <p class="text-secondary mb-4">Pilih paket berlangganan untuk akses tak terbatas ke seluruh berita eksklusif dan laporan mendalam kami.</p>
                
                <div class="mb-4">
                    <div class="form-check text-start p-3 border rounded mb-2 duration-option" onclick="document.getElementById('duration_1').click()">
                        <input class="form-check-input ms-0 me-2" type="radio" name="duration" id="duration_1" value="1" checked>
                        <label class="form-check-label w-100 d-flex justify-content-between align-items-center" for="duration_1">
                            <span><span class="fw-bold fs-5">1 Bulan</span></span>
                            <span class="fw-bold text-success fs-5">Rp50.000</span>
                        </label>
                    </div>
                    <div class="form-check text-start p-3 border rounded mb-2 duration-option" onclick="document.getElementById('duration_3').click()">
                        <input class="form-check-input ms-0 me-2" type="radio" name="duration" id="duration_3" value="3">
                        <label class="form-check-label w-100 d-flex justify-content-between align-items-center" for="duration_3">
                            <span><span class="fw-bold fs-5">3 Bulan</span> <span class="badge bg-danger ms-2">Hemat Rp10rb</span></span>
                            <span class="fw-bold text-success fs-5">Rp140.000</span>
                        </label>
                    </div>
                    <div class="form-check text-start p-3 border rounded mb-2 duration-option" onclick="document.getElementById('duration_12').click()">
                        <input class="form-check-input ms-0 me-2" type="radio" name="duration" id="duration_12" value="12">
                        <label class="form-check-label w-100 d-flex justify-content-between align-items-center" for="duration_12">
                            <span><span class="fw-bold fs-5">12 Bulan</span> <span class="badge bg-danger ms-2">Hemat Rp100rb</span></span>
                            <span class="fw-bold text-success fs-5">Rp500.000</span>
                        </label>
                    </div>
                </div>

                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Akses ke semua artikel premium</li>
                    <li><i class="fas fa-check-circle"></i> Tanpa iklan (Ad-free experience)</li>
                    <li><i class="fas fa-check-circle"></i> Laporan khusus & wawancara eksklusif</li>
                    <li><i class="fas fa-check-circle"></i> Mendukung jurnalisme independen</li>
                </ul>

                <button id="pay-button" class="btn btn-primary-custom w-100 py-3 mt-3 fw-bold fs-5">
                    Lanjutkan Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .duration-option {
        cursor: pointer;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.03);
    }
    .duration-option:hover {
        background: rgba(59, 130, 246, 0.1);
        border-color: var(--accent-color) !important;
    }
    .duration-option input[type="radio"] {
        cursor: pointer;
    }
</style>

<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= getenv('MIDTRANS_CLIENT_KEY') ?>"></script>
<script>
    document.getElementById('pay-button').onclick = function(){
        
        const selectedDuration = document.querySelector('input[name="duration"]:checked').value;

        // Mengambil snap token dari backend dengan durasi yang dipilih
        fetch('<?= base_url('subscribe/pay') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ duration: selectedDuration })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Memicu popup Snap
                snap.pay(data.snapToken, {
                    onSuccess: function(result){
                        // Fallback trigger for Localhost since Webhook can't reach it
                        fetch('<?= base_url('subscribe/finish_local') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ order_id: data.order_id })
                        }).then(() => {
                            alert("Pembayaran Berhasil! Terimakasih telah berlangganan.");
                            window.location.href = "<?= base_url() ?>";
                        });
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda!");
                    },
                    onError: function(result){
                        alert("Pembayaran gagal!");
                    },
                    onClose: function(){
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            } else {
                alert('Gagal menginisialisasi pembayaran: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan jaringan.');
        });
    };
</script>

</body>
</html>
