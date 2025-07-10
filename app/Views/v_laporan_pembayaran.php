<div class="card">
    <div class="card-header">
        <h4>Laporan Pembayaran & Pemesanan</h4>
    </div>
    <div class="card-body">
        <form method="post" action="<?= base_url('laporan/filter') ?>" class="form-inline mb-3">
            <input type="date" name="tanggal_mulai" class="form-control mr-2" required>
            <input type="date" name="tanggal_akhir" class="form-control mr-2" required>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <?php if ($laporan): ?>
        <a href="<?= base_url('laporan/excel') ?>" class="btn btn-success btn-sm">Export Excel</a>
        <a href="<?= base_url('laporan/pdf') ?>" class="btn btn-danger btn-sm">Export PDF</a>
        <br><br>
        <table class="table table-bordered">
            <thead class="bg-info text-white">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tgl Pesan</th>
                    <th>Jumlah Orang</th>
                    <th>Tgl Bayar</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($laporan as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nama_lengkap']) ?></td>
                    <td><?= $row['tanggal_pesan'] ?></td>
                    <td><?= $row['jumlah_orang'] ?></td>
                    <td><?= $row['tanggal_bayar'] ?></td>
                    <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php endif ?>
    </div>
</div>
