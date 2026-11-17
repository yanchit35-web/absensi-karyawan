# 🏢 Sistem Absensi Karyawan Berbasis CodeIgniter 4

Aplikasi manajemen kehadiran dan absensi karyawan modern berbasis web yang dibangun dengan framework **CodeIgniter 4**, **MySQL**, **Bootstrap 5**, dan **Leaflet.js**. Dilengkapi dengan fitur absensi mandiri menggunakan **Foto Selfie Kamera**, **GPS Geolocation & Geofencing Radius Kantor Akurat**, serta pemisahan hak akses ketat antara **Admin** dan **Karyawan**.

---

## 🌟 Fitur Unggulan

### 1. 🛡️ Role & Hak Akses Ketat (Strict Scoping)
- **Admin**:
  - Mengelola data master karyawan (CRUD, pencarian, status aktif/non-aktif).
  - Mengelola seluruh data absensi karyawan.
  - Menyetujui atau menolak permohonan **Cuti / Izin / Sakit** dan **Lembur**.
  - Mengatur konfigurasi kantor: jam kerja, toleransi keterlambatan, koordinat GPS kantor, dan radius kehadiran.
  - Melihat serta mengekspor laporan seluruh karyawan ke format **PDF** dan **Excel**.
- **Karyawan**:
  - Dashboard pribadi dengan tema modern *glassmorphism* (NIK, Jabatan, Departemen).
  - Melakukan absensi mandiri masuk & pulang.
  - Hanya dapat melihat data, riwayat kehadiran, pengajuan izin, dan laporan **miliknya sendiri**.

### 2. 📸 Self-Absen dengan Kamera Selfie & GPS Geofencing
- **Kamera Selfie**: Mengambil foto secara langsung dari kamera laptop/HP saat absensi masuk dan pulang.
- **Waktu Akurat (WIB)**: Sinkronisasi jam digital *real-time* berbasis zona waktu `Asia/Jakarta`.
- **Validasi Radius Kantor (Geofencing)**:
  - Menggunakan algoritma **Haversine Formula** di sisi server (backend) untuk menghitung jarak akurat antara koordinat GPS karyawan dengan kantor.
  - Menolak absensi secara otomatis apabila karyawan berada di luar radius yang telah diatur oleh admin.
  - Dilengkapi peta interaktif **Leaflet.js** & OpenStreetMap yang menampilkan titik koordinat Anda dan lingkaran batas radius kantor.

### 3. ⚙️ Pengaturan Kantor Interaktif
- Titik koordinat GPS kantor dapat ditentukan dengan mudah:
  - Memasukkan angka Latitude & Longitude manual.
  - Menggeser (*drag-and-drop*) marker pada peta Leaflet.js.
  - Mengklik tombol **"Gunakan Lokasi Saat Ini"** (auto-detect GPS).
- Pengaturan radius toleransi (meter), jam masuk, jam pulang, dan toleransi keterlambatan (menit).

### 4. 📝 Pengajuan Cuti, Izin, Sakit & Lembur
- Karyawan dapat mengajukan izin/cuti/sakit dengan keterangan dan unggahan dokumen bukti.
- Saat permohonan disetujui admin, sistem otomatis membuat riwayat absensi terkait.
- Pengajuan lembur dengan pencatatan waktu mulai, selesai, dan alasan pekerjaan.

### 5. 📊 Laporan & Ekspor Data
- Rekapitulasi absensi bulanan/rentang tanggal tertentu.
- Ekspor laporan siap cetak ke format **PDF** (*Dompdf*).
- Ekspor rekapitulasi ke format **Excel (.xlsx)** (*PhpSpreadsheet*).

---

## 🛠️ Tech Stack & Requirements

- **PHP**: `^8.2` atau `^8.3` (Disarankan PHP 8.3 via Laragon / XAMPP)
- **Framework**: CodeIgniter 4.7.5
- **Database**: MySQL 8.x / MariaDB
- **Frontend**: Bootstrap 5.3, Bootstrap Icons, Leaflet.js (OpenStreetMap)
- **Libraries**:
  - `dompdf/dompdf`: Ekspor dokumen PDF
  - `phpoffice/phpspreadsheet`: Ekspor spreadsheet Excel
- **Zona Waktu**: `Asia/Jakarta` (WIB)

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Clone Repositori
```bash
git clone git@github.com:yanchit35-web/absensi-karyawan.git
cd absensi-karyawan
```

### 2. Install Dependensi (Composer)
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database jika diperlukan:
```env
database.default.hostname = localhost
database.default.database = absensi_karyawan
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306

app.appTimezone = 'Asia/Jakarta'
```

### 4. Buat Database & Jalankan Migrasi + Seeder
Buat database baru bernama `absensi_karyawan` di MySQL Anda, kemudian jalankan:
```bash
# Jalankan seluruh migrasi tabel
php spark migrate

# Jalankan seeder akun & data awal
php spark db:seed UserSeeder
php spark db:seed DummySeeder
```

### 5. Jalankan Web Server
```bash
php spark serve --port 8080
```
Buka browser dan akses: **[http://localhost:8080](http://localhost:8080)**

---

## 🔑 Akun Demo Default

| Role | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin123` | Akses penuh ke seluruh menu & pengaturan |
| **Karyawan 1** | `emp001` | `password123` | Ahmad Fauzi - Software Engineer (IT) |
| **Karyawan 2** | `emp002` | `password123` | Siti Nurhaliza - HR Specialist (HRD) |
| **Karyawan 3** | `emp003` | `password123` | Budi Santoso - Finance Officer (Keuangan) |
| **Karyawan 4** | `emp004` | `password123` | Dewi Lestari - Marketing Lead (Pemasaran) |
| **Karyawan 5** | `emp005` | `password123` | Eko Prasetyo - Operations Staff (Operasional) |

---

## 📁 Struktur Direktori Penting

```text
absensi-karyawan/
├── app/
│   ├── Config/          # Konfigurasi aplikasi, rute, database, filter
│   ├── Controllers/     # Auth, Dashboard, Absensi, Karyawan, Laporan, Pengajuan, Lembur, Pengaturan
│   ├── Database/
│   │   ├── Migrations/  # Skema tabel database (Users, Karyawan, Absensi, Pengaturan, Pengajuan, Lembur)
│   │   └── Seeds/       # UserSeeder & DummySeeder data sampel
│   ├── Filters/         # AuthFilter untuk proteksi route login & session
│   ├── Models/          # Model data CodeIgniter
│   └── Views/           # Template UI (Blade-like views dengan Bootstrap 5 & Leaflet.js)
├── public/
│   ├── uploads/absensi/ # Penyimpanan foto selfie absensi
│   └── index.php        # Entry point web
├── .env.example         # Template konfigurasi environment
└── README.md            # Dokumentasi proyek
```

---

## 📄 Lisensi
Proyek ini dibuat untuk keperluan sistem manajemen internal dan absensi karyawan.
Bebas dikembangkan dan dimodifikasi sesuai kebutuhan organisasi/perusahaan Anda.
