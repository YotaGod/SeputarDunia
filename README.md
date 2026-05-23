# 🌍 Seputar Dunia - Portal Berita Modern

Selamat datang di **Seputar Dunia**! Ini adalah platform portal berita interaktif yang tidak cuma menyajikan artikel terkini, tapi juga memberikan pengalaman baca yang bersih, cepat, dan kekinian.

## ✨ Fitur Unggulan
- **Akses Premium & Berlangganan:** Terintegrasi langsung dengan Midtrans. Pembaca bisa berlangganan (paket 1 bulan, 3 bulan, atau 1 tahun) untuk menikmati konten eksklusif tanpa batasan.
- **Interaksi Pengguna:** Fitur *Like* dan *Dislike*, serta sistem kolom komentar yang dinamis.
- **Autentikasi Mudah:** Mendukung fitur *Login with Google* (Google OAuth) atau menggunakan akun standar.
- **Admin Panel Terpusat:** Dashboard khusus bagi editor untuk menulis berita, menerbitkan draf, hingga meninjau komentar dari pembaca.
- **Pembaruan Berita Global:** Terhubung dengan GNews API untuk mengambil tren berita dunia secara *real-time*.

## 🛠️ Teknologi di Balik Layar
Aplikasi ini dibangun menggunakan:
- **Backend:** CodeIgniter 4 (PHP Framework)
- **Frontend:** HTML5, Bootstrap 5, jQuery, dan Custom CSS (Modern UI)
- **Database:** MySQL
- **Layanan Pihak Ketiga:** Midtrans Payment Gateway, Google API Client, GNews API.

## 🚀 Cara Menjalankan di Komputer Sendiri (Localhost)

Buat kamu yang mau berkontribusi atau sekadar mencoba menjalankan *project* ini secara lokal, ikuti langkah mudah berikut:

1. **Clone & Install Dependencies**
   Pastikan kamu sudah menginstal PHP dan Composer. Buka terminal dan jalankan:
   ```bash
   git clone https://github.com/username-kamu/seputardunia.git
   cd seputardunia
   composer install
   ```

2. **Atur Database & Environment**
   - Buat database baru di MySQL kamu (misalnya `seputardunia_db`).
   - Ubah nama file konfigurasi dari `env` menjadi `.env`.
   - Buka file `.env` tersebut dan isi bagian koneksi database:
     ```env
     database.default.hostname = localhost
     database.default.database = seputardunia_db
     database.default.username = root
     database.default.password = 
     ```
   - Masukkan *API Keys* (Midtrans, Google, GNews) milikmu di bagian bawah file tersebut.

3. **Jalankan Aplikasi**
   Kamu bisa memanfaatkan fitur *serve* dari CodeIgniter:
   ```bash
   php spark serve
   ```
   Aplikasi kini bisa diakses melalui peramban pada alamat `http://localhost:8080`.

---
Dibuat dengan ❤️ untuk Jurnalisme yang Lebih Baik.
