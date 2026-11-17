<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\KaryawanModel;

class LaporanController extends BaseController
{
    protected $absensiModel;
    protected $karyawanModel;

    public function __construct()
    {
        $this->absensiModel  = new AbsensiModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');

        $karyawan    = ($role === 'admin') ? $this->karyawanModel->getAktif() : [];
        $departemens = ($role === 'admin') ? $this->karyawanModel->getDepartemen() : [];

        $bulan = date('m');
        $tahun = date('Y');

        // Jika karyawan, langsung tampilkan laporan miliknya
        $absensi = [];
        if ($role === 'karyawan') {
            $absensi = $this->absensiModel->getLaporan($bulan, $tahun, $userKaryawanId, null);
        }

        $data = [
            'title'       => ($role === 'karyawan') ? 'Laporan Kehadiran Saya' : 'Laporan Rekap Absensi',
            'karyawan'    => $karyawan,
            'departemens' => $departemens,
            'absensi'     => $absensi,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'karyawan_id' => ($role === 'karyawan') ? $userKaryawanId : null,
            'departemen'  => null,
            'isKaryawan'  => ($role === 'karyawan'),
        ];

        return view('laporan/index', $data);
    }

    public function generate()
    {
        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');

        $bulan       = $this->request->getPost('bulan') ?? date('m');
        $tahun       = $this->request->getPost('tahun') ?? date('Y');
        
        // PENTING: Jika Karyawan, PAKSA karyawan_id ke miliknya sendiri!
        if ($role === 'karyawan') {
            $karyawan_id = $userKaryawanId;
            $departemen  = null;
        } else {
            $karyawan_id = $this->request->getPost('karyawan_id') ?: null;
            $departemen  = $this->request->getPost('departemen') ?: null;
        }

        $absensi     = $this->absensiModel->getLaporan($bulan, $tahun, $karyawan_id, $departemen);
        $karyawan    = ($role === 'admin') ? $this->karyawanModel->getAktif() : [];
        $departemens = ($role === 'admin') ? $this->karyawanModel->getDepartemen() : [];

        $data = [
            'title'       => ($role === 'karyawan') ? 'Laporan Kehadiran Saya' : 'Laporan Rekap Absensi',
            'absensi'     => $absensi,
            'karyawan'    => $karyawan,
            'departemens' => $departemens,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'karyawan_id' => $karyawan_id,
            'departemen'  => $departemen,
            'isKaryawan'  => ($role === 'karyawan'),
        ];

        return view('laporan/index', $data);
    }

    public function exportPdf()
    {
        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');

        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // PENTING: Karyawan HANYA bisa ekspor data miliknya sendiri
        if ($role === 'karyawan') {
            $karyawan_id = $userKaryawanId;
            $departemen  = null;
        } else {
            $karyawan_id = $this->request->getGet('karyawan_id') ?: null;
            $departemen  = $this->request->getGet('departemen') ?: null;
        }

        $absensi = $this->absensiModel->getLaporan($bulan, $tahun, $karyawan_id, $departemen);

        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ];

        $karyawanInfo = null;
        if ($karyawan_id) {
            $karyawanInfo = $this->karyawanModel->find($karyawan_id);
        }

        $data = [
            'absensi'      => $absensi,
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'namaBulan'    => $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan,
            'departemen'   => $departemen,
            'karyawanInfo' => $karyawanInfo,
            'isKaryawan'   => ($role === 'karyawan'),
        ];

        $html = view('laporan/pdf', $data);

        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream(
            'laporan-absensi-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . $tahun . '.pdf',
            ['Attachment' => true]
        );
        exit;
    }

    public function exportExcel()
    {
        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');

        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // PENTING: Karyawan HANYA bisa ekspor data miliknya sendiri
        if ($role === 'karyawan') {
            $karyawan_id = $userKaryawanId;
            $departemen  = null;
        } else {
            $karyawan_id = $this->request->getGet('karyawan_id') ?: null;
            $departemen  = $this->request->getGet('departemen') ?: null;
        }

        $data = $this->absensiModel->getLaporan($bulan, $tahun, $karyawan_id, $departemen);

        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absensi');

        // Title
        $bulanLabel = $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan;
        $sheet->mergeCells('A1:J1');
        $titlePrefix = ($role === 'karyawan') ? "LAPORAN KEHADIRAN PRIBADI" : "LAPORAN ABSENSI KARYAWAN";
        $sheet->setCellValue('A1', "{$titlePrefix} - {$bulanLabel} {$tahun}");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'NIK', 'Nama', 'Jabatan', 'Departemen', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Keterangan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue([$i + 1, 3], $h);
        }

        // Style header
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '343a40']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A3:J3')->applyFromArray($headerStyle);

        // Data rows
        foreach ($data as $row => $item) {
            $rowNum = $row + 4;
            $sheet->setCellValue([1, $rowNum], $row + 1);
            $sheet->setCellValue([2, $rowNum], $item['nik']);
            $sheet->setCellValue([3, $rowNum], $item['nama']);
            $sheet->setCellValue([4, $rowNum], $item['jabatan']);
            $sheet->setCellValue([5, $rowNum], $item['departemen']);
            $sheet->setCellValue([6, $rowNum], $item['tanggal']);
            $sheet->setCellValue([7, $rowNum], $item['jam_masuk'] ?? '-');
            $sheet->setCellValue([8, $rowNum], $item['jam_keluar'] ?? '-');
            $sheet->setCellValue([9, $rowNum], strtoupper($item['status']));
            $sheet->setCellValue([10, $rowNum], $item['keterangan'] ?? '');

            // Color by status
            $statusColors = [
                'hadir'     => 'd4edda',
                'terlambat' => 'fff3cd',
                'izin'      => 'cce5ff',
                'sakit'     => 'ffeeba',
                'alpha'     => 'f8d7da',
            ];
            $color = $statusColors[$item['status']] ?? 'FFFFFF';
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($color);

            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()
                ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'laporan-absensi-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . $tahun . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
