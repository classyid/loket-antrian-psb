# Sistem Antrian Multi-Loket PSB

Sistem Antrian Multi-Loket untuk Penerimaan Siswa Baru (PSB). Aplikasi ini dirancang untuk mengelola antrian secara efisien dengan beberapa loket dan dapat mengirimkan notifikasi WhatsApp kepada pendaftar.

## Fitur Utama

- **Multi Loket**: Menangani antrian dengan beberapa loket yang memanggil nomor antrian secara terurut.
- **Notifikasi WhatsApp**: Mengirimkan pesan otomatis ke pendaftar mengenai nomor antrian dan loket.
- **Reset Sistem**: Menyediakan opsi untuk mereset sistem dan mulai kembali dari nomor antrian pertama.
- **Real-time Update**: Dashboard dengan pembaruan langsung tentang status antrian dan statistik.

## Instalasi

Untuk menjalankan sistem ini di server lokal atau server produksi, ikuti langkah-langkah berikut:

### Prasyarat

1. **PHP** 7.0 atau lebih tinggi
2. **MySQL** atau **MariaDB**
3. **cURL** (untuk integrasi API WhatsApp)

### Langkah Instalasi

1. **Clone Repository**:

git clone [https://github.com/your-username/antrian-sistem-multi-loket.git](https://github.com/your-username/antrian-sistem-multi-loket.git)
cd antrian-sistem-multi-loket

````

2. **Buat Database**:
Buat database di MySQL dengan nama `psb2025` (atau nama lain sesuai keinginan) dan jalankan query SQL berikut untuk membuat tabel `pendaftar`:

```sql
CREATE TABLE pendaftar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    whatsapp VARCHAR(20),
    nomor_antrian INT,
    loket_id VARCHAR(50) DEFAULT NULL,
    status ENUM('waiting', 'called', 'done') DEFAULT 'waiting'
);
````

3. **Konfigurasi Database**:
   Sesuaikan file `config.php` dengan kredensial database Anda:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your-db-user');
   define('DB_PASS', 'your-db-password');
   define('DB_NAME', 'psb2025');
   ```

4. **Integrasi API WhatsApp**:
   Pastikan untuk mengganti API key dan sender ID di `config.php` dengan informasi yang valid:

   ```php
   define('API_KEY', 'your-api-key');
   define('SENDER', 'your-sender-id');
   ```

5. **Menjalankan Aplikasi**:
   Setelah konfigurasi selesai, Anda bisa membuka aplikasi di browser:

   ```
   http://localhost/antrian-sistem-multi-loket/index.php
   ```

## Penggunaan

1. **Login Loket**: Masukkan ID Loket untuk mengakses kontrol dan memanggil nomor antrian.
2. **Kontrol Antrian**: Di halaman kontrol, Anda dapat memanggil nomor antrian berikutnya dan mengatur statusnya.
3. **Dashboard**: Tampilkan statistik dan nomor antrian yang sedang berjalan di dashboard secara real-time.

## Teknologi yang Digunakan

* **PHP** untuk back-end
* **MySQL** untuk database
* **AJAX** untuk pembaruan real-time
* **WhatsApp API** untuk pengiriman notifikasi
* **HTML, CSS** untuk tampilan front-end

## Kontribusi

Jika Anda ingin berkontribusi pada proyek ini, fork repository ini dan kirimkan pull request. Kami menerima kontribusi dalam bentuk fitur baru, perbaikan bug, dan dokumentasi yang lebih baik.

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

```
