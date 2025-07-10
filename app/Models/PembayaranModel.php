<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_bayar';
    protected $allowedFields = ['id_pesan', 'tanggal_bayar', 'jumlah_bayar', 'metode', 'bukti_bayar', 'status'];

    public function getAllWithPesanan()
    {
        return $this->select('pembayaran.*, pesan.nama_lengkap')
                    ->join('pesan', 'pesan.id_pesan = pembayaran.id_pesan', 'left')
                    ->orderBy('tanggal_bayar', 'DESC')
                    ->findAll();
    }

    public function getLaporan($mulai, $akhir)
    {
        return $this->select('pembayaran.*, pesan.nama_lengkap, pesan.tanggal_pesan, pesan.jumlah_orang')
                    ->join('pesan', 'pesan.id_pesan = pembayaran.id_pesan', 'left')
                    ->where('tanggal_bayar >=', $mulai)
                    ->where('tanggal_bayar <=', $akhir)
                    ->orderBy('tanggal_bayar', 'DESC')
                    ->findAll();
    }
}
