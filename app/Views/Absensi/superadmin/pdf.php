<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-weight: bold;
            font-size: 14pt;
            margin: 0;
        }

        .company-address {
            font-size: 9pt;
            margin: 5px 0;
            color: #333;
        }

        .report-title {
            font-weight: bold;
            font-size: 12pt;
            margin: 10px 0;
            text-decoration: underline;
        }

        .report-info {
            margin-bottom: 15px;
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        .table-header {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .table-footer {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="company-name">PT. APLINUSA LINTASARTA</h1>
        <p class="company-address">
            Jakarta Pusat, Menara Thamrin 12th Floor<br>
            Jl. M.H. Thamrin Kav.3 Jakarta 10250<br>
            Telepon: +6221 230 2345 | Email: info@lintasarta.co.id
        </p>
        <hr>
        <h2 class="report-title">LAPORAN ABSENSI</h2>
        <div class="report-info">
            <table style="border:none;">
                <tr>
                    <td style="border:none; width: 30%;">Periode</td>
                    <td style="border:none;">: <?= $selectedMonth ?></td>
                </tr>
                <tr>
                    <td style="border:none;">Tanggal Cetak</td>
                    <td style="border:none;">: <?= date('d F Y') ?></td>
                </tr>
                <?php if (isset($selectedUser) && $selectedUser !== 'all'): ?>
                    <tr>
                        <td style="border:none;">User</td>
                        <td style="border:none;">: <?= $userName ?? '-' ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (isset($selectedCategory) && $selectedCategory): ?>
                    <tr>
                        <td style="border:none;">Kategori</td>
                        <td style="border:none;">: <?= $categoryName ?? '-' ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <?php if (empty($absensi)): ?>
        <div class="no-data">
            Tidak ada data absensi untuk periode yang dipilih
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr class="table-header">
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">User</th>
                    <th style="width: 8%;">Tanggal</th>
                    <th style="width: 12%;">Kategori</th>
                    <th style="width: 15%;">Judul Kegiatan</th>
                    <th style="width: 7%;">Jam Masuk</th>
                    <th style="width: 7%;">Jam Keluar</th>
                    <th style="width: 8%;">Tanggal Keluar</th>
                    <th style="width: 9%;">Total Jam</th>
                    <th style="width: 12%;">Kegiatan Harian</th>
                    <th style="width: 8%;">No Tiket</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $totalHours = 0;
                foreach ($absensi as $item):
                    // Hitung total jam
                    if ($item['jam_masuk'] && $item['jam_keluar']) {
                        $tanggal_keluar = !empty($item['tanggal_keluar']) ? $item['tanggal_keluar'] : $item['tanggal'];
                        $masuk = strtotime($item['tanggal'] . ' ' . $item['jam_masuk']);
                        $keluar = strtotime($tanggal_keluar . ' ' . $item['jam_keluar']);
                        $diffMinutes = ($keluar - $masuk) / 60;
                        $hours = floor($diffMinutes / 60);
                        $minutes = $diffMinutes % 60;
                        $totalHoursItem = sprintf('%d jam %02d menit', $hours, $minutes);
                        $totalHoursDecimal = $diffMinutes / 60;
                    } else {
                        $totalHoursItem = '-';
                        $totalHoursDecimal = 0;
                    }
                    $totalHours += $totalHoursDecimal;
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= esc($item['user_name']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                        <td><?= esc($item['category_name']) ?></td>
                        <td><?= esc($item['judul_kegiatan']) ?></td>
                        <td class="text-center"><?= $item['jam_masuk'] ? date('H:i', strtotime($item['jam_masuk'])) : '-' ?></td>
                        <td class="text-center"><?= $item['jam_keluar'] ? date('H:i', strtotime($item['jam_keluar'])) : '-' ?></td>
                        <td class="text-center"><?= $item['tanggal_keluar'] ? date('d/m/Y', strtotime($item['tanggal_keluar'])) : '-' ?></td>
                        <td class="text-center"><?= $totalHoursItem ?></td>
                        <td><?= nl2br(esc($item['kegiatan_harian'])) ?></td>
                        <td class="text-center"><?= esc($item['no_tiket']) ?: '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-footer">
                    <td colspan="8" class="text-right">Total Jam</td>
                    <td colspan="3" class="text-center"><?= sprintf('%.2f jam', $totalHours) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="signature">
            <p>
                Jakarta, <?= date('d F Y') ?><br>
                Mengetahui,<br>
                Kepala Departemen
                <br><br><br><br>
                ______________________<br>
                NIP.
            </p>
        </div>
    <?php endif; ?>
</body>

</html>