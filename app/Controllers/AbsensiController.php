<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\KaryawanModel;
use App\Models\PengaturanModel;

class AbsensiController extends BaseController
{
    protected $absensiModel;
    protected $karyawanModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->absensiModel    = new AbsensiModel();
        $this->karyawanModel   = new KaryawanModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $role        = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');

        $tanggal    = $this->request->getGet('tanggal');
        $status     = $this->request->getGet('status');

        // Integrasi SQL View: vw_rekap_absensi_harian
        // Menampilkan rekap harian, kalkulasi menit keterlambatan, dan status shift
        try {
            $db = \Config\Database::connect();
            $viewBuilder = $db->table('vw_rekap_absensi_harian');

            if ($role === 'karyawan') {
                $karyawan_id = $userKaryawanId;
                $viewBuilder->where('karyawan_id', $userKaryawanId);
            } else {
                $karyawan_id = $this->request->getGet('karyawan_id');
                if ($karyawan_id) {
                    $viewBuilder->where('karyawan_id', $karyawan_id);
                }
            }

            if ($tanggal) {
                $viewBuilder->where('tanggal', $tanggal);
            }

            if ($status) {
                $viewBuilder->where('status', $status);
            }

            $absensi = $viewBuilder->orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->get()->getResultArray();
        } catch (\Throwable $e) {
            // Fallback graceful ke query builder bawaan jika view belum tersedia
            $builder = $this->absensiModel
                ->select('absensi.*, karyawan.nama, karyawan.nik, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = absensi.karyawan_id');

            if ($role === 'karyawan') {
                $karyawan_id = $userKaryawanId;
                $builder->where('absensi.karyawan_id', $userKaryawanId);
            } else {
                $karyawan_id = $this->request->getGet('karyawan_id');
                if ($karyawan_id) {
                    $builder->where('absensi.karyawan_id', $karyawan_id);
                }
            }

            if ($tanggal) {
                $builder->where('absensi.tanggal', $tanggal);
            }

            if ($status) {
                $builder->where('absensi.status', $status);
            }

            $absensi = $builder->orderBy('absensi.tanggal', 'DESC')->orderBy('absensi.created_at', 'DESC')->findAll();
        }

        $karyawan = ($role === 'admin') ? $this->karyawanModel->getAktif() : [];

        $data = [
            'title'       => ($role === 'karyawan') ? 'Riwayat Absensi Saya' : 'Rekap Absensi Karyawan',
            'absensi'     => $absensi,
            'karyawan'    => $karyawan,
            'tanggal'     => $tanggal,
            'karyawan_id' => $karyawan_id,
            'status'      => $status,
            'isKaryawan'  => ($role === 'karyawan'),
        ];

        return view('absensi/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Hanya Admin yang dapat input manual absensi!');
        }

        $karyawan = $this->karyawanModel->getAktif();

        $data = [
            'title'      => 'Input Absensi Manual',
            'karyawan'   => $karyawan,
            'validation' => null,
        ];

        return view('absensi/create', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $rules = [
            'karyawan_id' => 'required|integer',
            'tanggal'     => 'required|valid_date[Y-m-d]',
            'status'      => 'required|in_list[hadir,izin,sakit,alpha,terlambat]',
            'jam_masuk'   => 'permit_empty',
            'jam_keluar'  => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return view('absensi/create', [
                'title'      => 'Input Absensi Manual',
                'karyawan'   => $this->karyawanModel->getAktif(),
                'validation' => $this->validator,
            ]);
        }

        $existing = $this->absensiModel
            ->where('karyawan_id', $this->request->getPost('karyawan_id'))
            ->where('tanggal', $this->request->getPost('tanggal'))
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Karyawan ini sudah memiliki data absensi pada tanggal tersebut!')
                ->withInput();
        }

        $this->absensiModel->insert([
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'tanggal'     => $this->request->getPost('tanggal'),
            'jam_masuk'   => $this->request->getPost('jam_masuk') ?: null,
            'jam_keluar'  => $this->request->getPost('jam_keluar') ?: null,
            'status'      => $this->request->getPost('status'),
            'keterangan'  => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/absensi')->with('success', 'Absensi berhasil disimpan!');
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $absensi  = $this->absensiModel->find($id);
        $karyawan = $this->karyawanModel->getAktif();

        if (!$absensi) {
            return redirect()->to('/absensi')->with('error', 'Data absensi tidak ditemukan!');
        }

        $data = [
            'title'      => 'Edit Absensi',
            'absensi'    => $absensi,
            'karyawan'   => $karyawan,
            'validation' => null,
        ];

        return view('absensi/edit', $data);
    }

    public function update($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to('/absensi')->with('error', 'Data absensi tidak ditemukan!');
        }

        $this->absensiModel->update($id, [
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'tanggal'     => $this->request->getPost('tanggal'),
            'jam_masuk'   => $this->request->getPost('jam_masuk') ?: null,
            'jam_keluar'  => $this->request->getPost('jam_keluar') ?: null,
            'status'      => $this->request->getPost('status'),
            'keterangan'  => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/absensi')->with('success', 'Absensi berhasil diupdate!');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to('/absensi')->with('error', 'Data absensi tidak ditemukan!');
        }

        $this->absensiModel->delete($id);
        return redirect()->to('/absensi')->with('success', 'Absensi berhasil dihapus!');
    }

    /**
     * Tampilan form Self-Absen dengan Kamera & GPS
     */
    public function selfAbsen()
    {
        $karyawan_id = session()->get('karyawan_id');
        if (!$karyawan_id) {
            return redirect()->to('/dashboard')->with('error', 'Akun tidak terhubung dengan profil karyawan!');
        }

        $setting  = $this->pengaturanModel->getSettings();
        $existing = $this->absensiModel->cekAbsenHariIni($karyawan_id);
        $karyawan = $this->karyawanModel->find($karyawan_id);

        $shift = $karyawan['shift'] ?? 'pagi';
        if ($shift === 'siang') {
            $shiftLabel     = 'Shift Siang';
            $jamMasukShift  = $setting['shift_siang_masuk'] ?? '14:00:00';
            $jamKeluarShift = $setting['shift_siang_keluar'] ?? '22:00:00';
        } elseif ($shift === 'malam') {
            $shiftLabel     = 'Shift Malam';
            $jamMasukShift  = $setting['shift_malam_masuk'] ?? '22:00:00';
            $jamKeluarShift = $setting['shift_malam_keluar'] ?? '06:00:00';
        } else {
            $shiftLabel     = 'Shift Pagi';
            $jamMasukShift  = $setting['shift_pagi_masuk'] ?? ($setting['jam_masuk'] ?? '08:00:00');
            $jamKeluarShift = $setting['shift_pagi_keluar'] ?? ($setting['jam_keluar'] ?? '17:00:00');
        }

        $data = [
            'title'          => 'Presensi Mandiri (Self-Absen)',
            'setting'        => $setting,
            'existing'       => $existing,
            'karyawan'       => $karyawan,
            'shiftLabel'     => $shiftLabel,
            'jamMasukShift'  => $jamMasukShift,
            'jamKeluarShift' => $jamKeluarShift,
        ];

        return view('absensi/self_absen', $data);
    }

    /**
     * Proses submit Self-Absen (Foto Selfie + GPS Akurat)
     */
    public function submitSelfAbsen()
    {
        $karyawan_id = session()->get('karyawan_id');
        if (!$karyawan_id) {
            return redirect()->to('/dashboard')->with('error', 'Akun tidak terhubung dengan profil karyawan!');
        }

        $setting   = $this->pengaturanModel->getSettings();
        $latitude  = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $fotoBase64= $this->request->getPost('foto');
        $tipeAbsen = $this->request->getPost('tipe'); // 'masuk' atau 'keluar'

        // 1. Validasi Kehadiran Titik Koordinat GPS
        if (empty($latitude) || empty($longitude) || !is_numeric($latitude) || !is_numeric($longitude)) {
            return redirect()->back()->with('error', 'Titik koordinat GPS tidak terdeteksi! Pastikan izin lokasi (GPS) pada browser/perangkat Anda sudah diaktifkan.');
        }

        // 2. Validasi Jarak ke Titik Koordinat Kantor yang telah disetting Admin
        $officeLat = (float)($setting['kantor_lat'] ?? -6.200000);
        $officeLng = (float)($setting['kantor_lng'] ?? 106.816666);
        $maxRadius = (int)($setting['kantor_radius'] ?? 500);

        $userLat = (float)$latitude;
        $userLng = (float)$longitude;

        $distance = $this->calculateDistance($userLat, $userLng, $officeLat, $officeLng);

        if ($distance > $maxRadius) {
            return redirect()->back()->with('error', "Gagal Absen! Anda berada di luar area kantor yang telah ditetapkan. Jarak Anda saat ini: {$distance} meter (Batas radius maksimal: {$maxRadius} meter). Silakan mendekat ke lokasi kantor untuk melakukan absensi.");
        }

        // 3. Simpan Foto Selfie (jika ada)
        $fotoNama = null;
        if (!empty($fotoBase64)) {
            $fotoData = explode(',', $fotoBase64);
            if (count($fotoData) > 1) {
                $decoded = base64_decode($fotoData[1]);
                $fotoNama = 'absen_' . $karyawan_id . '_' . date('Ymd_His') . '.jpg';
                $uploadDir = FCPATH . 'uploads/absensi/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                file_put_contents($uploadDir . $fotoNama, $decoded);
            }
        }

        $existing = $this->absensiModel->cekAbsenHariIni($karyawan_id);
        $currentTime = date('H:i:s');
        $currentDate = date('Y-m-d');

        // Jika absen PULANG
        if ($tipeAbsen === 'keluar' || ($existing && !$existing['jam_keluar'])) {
            if ($existing) {
                $keteranganLama = $existing['keterangan'] ?? '';
                $keteranganBaru = $keteranganLama . ' | Pulang: ' . date('H:i') . " (Jarak {$distance}m)";
                $this->absensiModel->update($existing['id'], [
                    'jam_keluar' => $currentTime,
                    'keterangan' => trim($keteranganBaru, ' |'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                return redirect()->to('/dashboard')->with('success', 'Absensi PULANG berhasil dicatat pukul ' . date('H:i') . " WIB (Lokasi valid: {$distance}m dari kantor).");
            }
        }

        if ($existing) {
            return redirect()->to('/dashboard')->with('info', 'Anda sudah melakukan absensi masuk dan pulang hari ini!');
        }

        // 4. Hitung Status Keterlambatan Otomatis & Akurat Sesuai Shift Karyawan
        $karyawan = $this->karyawanModel->find($karyawan_id);
        $userShift = $karyawan['shift'] ?? 'pagi';

        if ($userShift === 'siang') {
            $shiftLabel      = 'Shift Siang';
            $jamMasukSetting = $setting['shift_siang_masuk'] ?? '14:00:00';
        } elseif ($userShift === 'malam') {
            $shiftLabel      = 'Shift Malam';
            $jamMasukSetting = $setting['shift_malam_masuk'] ?? '22:00:00';
        } else {
            $shiftLabel      = 'Shift Pagi';
            $jamMasukSetting = $setting['shift_pagi_masuk'] ?? ($setting['jam_masuk'] ?? '08:00:00');
        }

        $toleransiMenit  = (int)($setting['toleransi_menit'] ?? 15);
        $batasToleransi  = date('H:i:s', strtotime("+$toleransiMenit minutes", strtotime($jamMasukSetting)));

        $status = 'hadir';
        $keterangan = "Tepat waktu [{$shiftLabel}: " . substr($jamMasukSetting, 0, 5) . "] (Jarak: {$distance}m)";
        if ($currentTime > $batasToleransi) {
            $status = 'terlambat';
            $selisihDetik = strtotime($currentTime) - strtotime($jamMasukSetting);
            $menitTelat = ceil($selisihDetik / 60);
            $keterangan = "Terlambat {$menitTelat} menit [{$shiftLabel}: " . substr($jamMasukSetting, 0, 5) . ", Jarak: {$distance}m]";
        }

        $this->absensiModel->insert([
            'karyawan_id' => $karyawan_id,
            'tanggal'     => $currentDate,
            'jam_masuk'   => $currentTime,
            'status'      => $status,
            'keterangan'  => $keterangan,
            'foto'        => $fotoNama,
            'latitude'    => $latitude,
            'longitude'   => $longitude,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Absensi MASUK berhasil dicatat pukul ' . date('H:i') . " WIB (Status: " . ucfirst($status) . ", Lokasi valid: {$distance}m dari kantor).");
    }

    /**
     * Hitung Jarak 2 Titik Koordinat Menggunakan Stored Function SQL: fn_hitung_jarak_haversine
     * Dilengkapi fallback rumus trigonometri bola bumi Haversine di PHP untuk keandalan maksimal tanpa mengganggu Geofencing GPS.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // 1. Eksekusi Stored Function SQL: fn_hitung_jarak_haversine
        try {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT fn_hitung_jarak_haversine(?, ?, ?, ?) AS distance", [
                (float)$lat1, (float)$lon1, (float)$lat2, (float)$lon2
            ]);
            $row = $query->getRow();
            if ($row && $row->distance !== null) {
                return (int)$row->distance;
            }
        } catch (\Throwable $e) {
            log_message('notice', 'fn_hitung_jarak_haversine fallback: ' . $e->getMessage());
        }

        // 2. Fallback Rumus Haversine Matematis di PHP
        $earthRadius = 6371000; // Radius bumi dalam meter
        $lat1Rad = deg2rad((float)$lat1);
        $lat2Rad = deg2rad((float)$lat2);
        $deltaLat = deg2rad((float)$lat2 - (float)$lat1);
        $deltaLon = deg2rad((float)$lon2 - (float)$lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt(min(1.0, max(0.0, $a))), sqrt(max(0.0, 1 - $a)));

        return (int)round($earthRadius * $c);
    }
}
