# Dokumentasi Penggunaan Sistem Antrian Multi-Loket PSB

---

## 1. **Overview Sistem**

Sistem ini adalah aplikasi web untuk mengelola antrian penerimaan siswa baru (PSB) dengan beberapa loket (multi-loket). Setiap loket bisa memanggil nomor antrian secara berurutan tanpa tumpang tindih. Setiap pendaftar akan mendapatkan notifikasi WhatsApp saat nomor antrian mereka dipanggil.

---

## 2. **Fitur Utama**

* Multi-loket dengan nomor antrian global terurut.
* Notifikasi WhatsApp otomatis saat nomor antrian dipanggil.
* Dashboard real-time menampilkan status antrian.
* Panel admin untuk melihat dan mengupdate data pendaftar.
* Reset sistem antrian dengan mudah.

---

## 3. **Cara Menggunakan**

### 3.1 **Untuk Pendaftar**

* Pendaftar mengisi formulir pendaftaran (Google Form atau form web).
* Sistem menyimpan data dan memberikan nomor antrian otomatis.
* Pendaftar tinggal menunggu panggilan.
* Saat nomor antrian dipanggil, pendaftar akan menerima pesan WhatsApp otomatis dengan nomor antrian dan loket yang harus didatangi.

---

### 3.2 **Untuk Petugas Loket**

* Petugas membuka halaman login loket (`login_loket.php`) dan memasukkan ID loket (misal `loket_1`).
* Setelah login, petugas mengakses halaman kontrol (`control.php`).
* Petugas klik tombol **Panggil Nomor Berikutnya** untuk memanggil nomor antrian secara berurutan.
* Sistem otomatis mengupdate status nomor antrian tersebut menjadi `called` dan mengirim notifikasi WhatsApp ke pendaftar.
* Petugas dapat mereset sistem antrian jika diperlukan dengan tombol **Reset Sistem**.

---

### 3.3 **Untuk Admin**

* Admin login ke halaman `admin_login.php` dengan username dan password yang sudah disediakan.
* Setelah login, admin dapat melihat seluruh data pendaftar di halaman `admin.php`.
* Admin dapat melakukan pencarian data pendaftar berdasarkan nama, nomor WhatsApp, atau nomor antrian.
* Admin dapat mengubah status pendaftar (`waiting`, `called`, atau `done`) melalui dropdown di setiap baris data.
* Admin dapat logout melalui tombol logout di bagian bawah halaman.

---

## 4. **Setup dan Konfigurasi**

### 4.1 **Persiapan Database**

* Buat database MySQL dan tabel `pendaftar` dengan struktur berikut:

```sql
CREATE TABLE pendaftar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  whatsapp VARCHAR(20) NOT NULL,
  nomor_antrian INT NOT NULL UNIQUE,
  loket_id VARCHAR(50) DEFAULT NULL,
  status ENUM('waiting','called','done') DEFAULT 'waiting'
);
```

### 4.2 **File Konfigurasi**

* Edit file `config.php` untuk mengatur koneksi database dan API WhatsApp:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'username_db');
define('DB_PASS', 'password_db');
define('DB_NAME', 'nama_db');

define('API_KEY', 'your-api-key-whatsapp');
define('SENDER', 'your-sender-id-whatsapp');
```

---

### 4.3 **Sinkronisasi Data**

* Sinkronisasi data pendaftar dari Google Form ke database dapat dilakukan via `sync.php` yang mengakses Google Apps Script API.
* Jalankan `sync.php` secara manual atau jadwalkan via cron job agar data terbaru selalu tersedia.

---

## 5. **Arsitektur Sistem**

* **Frontend**:

  * `index.php`: Landing page dan informasi sistem.
  * `login_loket.php`: Login untuk petugas loket.
  * `control.php`: Halaman kontrol loket untuk panggil nomor.
  * `dashboard.php`: Dashboard real-time untuk monitoring.
  * `admin.php`: Panel admin untuk kelola data pendaftar.
  * `admin_login.php`: Login admin.

* **Backend**:

  * `functions.php`: Fungsi umum termasuk koneksi database dan API WhatsApp.
  * `sync.php`: Sinkronisasi data pendaftar dari Google Sheets.
  * `dashboard_api.php`: API JSON untuk dashboard.

---

## 6. **Penggunaan API WhatsApp**

* Sistem menggunakan API WhatsApp dari penyedia pihak ketiga (`v8.wa.bangkitsolusibangsa.id`).
* Pesan WhatsApp otomatis dikirim saat nomor antrian dipanggil.
* Pastikan API key dan sender sudah benar agar pengiriman pesan sukses.

---

## 7. **Tips Pemeliharaan**

* Selalu backup database secara rutin.
* Pantau log error server untuk mengetahui masalah teknis.
* Update API key WhatsApp jika terjadi perubahan dari provider.
* Pastikan koneksi internet stabil untuk pengiriman notifikasi.

---

## 8. **Pertanyaan Umum (FAQ)**

**Q: Bagaimana jika ada pendaftar baru setelah antrian berjalan?**
A: Pendaftar baru otomatis mendapatkan nomor antrian terakhir +1 dan akan dipanggil setelah nomor antrian sebelumnya selesai.

**Q: Bagaimana reset sistem antrian?**
A: Petugas loket atau admin dapat menggunakan tombol reset untuk mengembalikan semua status pendaftar ke `waiting` dan mengosongkan loket yang memanggil.

**Q: Apakah sistem bisa menangani lebih dari 1 loket?**
A: Ya, sistem sudah mendukung multi-loket dengan nomor antrian global yang terurut.

---

## 9. **Kontak dan Bantuan**

Jika membutuhkan bantuan teknis atau fitur tambahan, silakan hubungi:
Email: [kontak@classy.id](mailto:kontak@classy.id)
Telepon: +62 812 4131 4446
