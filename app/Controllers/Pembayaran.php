<?php

namespace App\Controllers;

use App\Models\PembayaranModel;
use App\Models\PemesananModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class Pembayaran extends BaseController
{
    protected $pembayaranModel;
    protected $pemesananModel;

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->pemesananModel = new PemesananModel();
    }

    public function index()
    {
        $data = [
            'judul' => 'Kelola Pembayaran',
            'page'  => 'v_pembayaran',
            'pembayaran' => $this->pembayaranModel->getAllWithPesanan()
        ];
        return view('v_template_backend', $data);
    }

    public function verifikasi($id)
    {
        $this->pembayaranModel->update($id, ['status' => 'terverifikasi']);
        return redirect()->to('/pembayaran')->with('success', 'Pembayaran diverifikasi');
    }

    public function gagal($id)
    {
        $this->pembayaranModel->update($id, ['status' => 'gagal']);
        return redirect()->to('/pembayaran')->with('error', 'Pembayaran digagalkan');
    }

    public function hapus($id)
    {
        $this->pembayaranModel->delete($id);
        return redirect()->to('/pembayaran')->with('success', 'Data dihapus');
    }

    public function laporan()
    {
        $data = [
            'judul' => 'Laporan Pembayaran & Pemesanan',
            'page'  => 'v_laporan_pembayaran',
            'laporan' => []
        ];
        return view('v_template_backend', $data);
    }

    public function filter()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        session()->set('tgl_awal', $tgl_awal);
        session()->set('tgl_akhir', $tgl_akhir);

        $data = [
            'judul' => 'Laporan Pembayaran',
            'page' => 'pembayaran/v_laporan',
            'laporan' => $this->pembayaranModel->getLaporan($tgl_awal, $tgl_akhir),
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
        ];
        return view('v_template_backend', $data);
    }

    public function exportExcel()
    {
        $tgl_awal = session()->get('tgl_awal') ?? date('Y-m-01');
        $tgl_akhir = session()->get('tgl_akhir') ?? date('Y-m-d');

        $data = $this->pembayaranModel->getLaporan($tgl_awal, $tgl_akhir);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Tanggal Pesan');
        $sheet->setCellValue('D1', 'Jumlah Orang');
        $sheet->setCellValue('E1', 'Tanggal Bayar');
        $sheet->setCellValue('F1', 'Jumlah Bayar');
        $sheet->setCellValue('G1', 'Status');

        // Data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['nama_lengkap']);
            $sheet->setCellValue('C' . $row, $item['tanggal_pesan']);
            $sheet->setCellValue('D' . $row, $item['jumlah_orang']);
            $sheet->setCellValue('E' . $row, $item['tanggal_bayar']);
            $sheet->setCellValue('F' . $row, $item['jumlah_bayar']);
            $sheet->setCellValue('G' . $row, $item['status']);
            $row++;
        }

        // Output Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_pembayaran.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }

    public function exportPDF()
    {
        $tgl_awal = session()->get('tgl_awal') ?? date('Y-m-01');
        $tgl_akhir = session()->get('tgl_akhir') ?? date('Y-m-d');

        $data = [
            'laporan' => $this->pembayaranModel->getLaporan($tgl_awal, $tgl_akhir),
        ];

        $html = view('v_laporan_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan_pembayaran.pdf', ['Attachment' => true]);
    }

}
