<!DOCTYPE html>
<html>

<head>
    <title>Rekapitulasi Lembur</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            height: auto;
            display: block;
            margin-right: 25px;
        }

        .header-text {
            text-align: center;
            font-weight: bold;
        }

        .employee-info {
            margin-bottom: 30px;
        }

        .employee-info table {
            border: none;
            width: 100%;
            /* Menyesuaikan lebar tabel dengan kontainer */
            table-layout: fixed;
            /* Mengatur agar lebar kolom proporsional */
        }

        .employee-info td {
            padding: 5px;
            border: none;
            text-align: left;
        }

        .employee-info td:first-child {
            width: 30%;
            /* Memberikan lebar yang lebih kecil untuk kolom kiri */
        }

        .employee-info td:nth-child(2) {
            width: 70%;
            /* Memberikan lebih banyak ruang untuk kolom kanan */
        }

        .overtime-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        .overtime-table th,
        .overtime-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }

        .overtime-table td {
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .overtime-table th {
            background-color: #f4801e;
            color: white;
        }

        .description {
            text-align: left;
        }

        .signature-section {
            width: 100%;
            margin-top: 30px;
            clear: both;
        }

        .signature-box-left {
            float: left;
            text-align: center;
            width: 200px;
        }

        .signature-box-right {
            float: right;
            text-align: center;
            width: 200px;
        }

        .signature-space {
            height: 40px;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .container {
            width: 100%;
            margin-bottom: 20px;
        }

        .form-section {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Recap Page -->
    <div class="container">
        <div class="header">
            <img src="<?= $logo_path ?>" alt="Logo Lintas" class="logo" style="width: 70px; height: 40px; object-fit: cover;">
            <div class="header-text">
                <strong>APLIKANUSA LINTASARTA</strong><br>
                REKAPITULASI LEMBUR (NON SHIFT)
            </div>
        </div>

        <div class="employee-info">
            <table>
                <tr>
                    <td>NAMA</td>
                    <td>: <?= user()->username ?></td>
                </tr>
                <tr>
                    <td>JABATAN</td>
                    <td>: <?= user()->position ?? '-' ?></td>
                </tr>
                <tr>
                    <td>DEPT</td>
                    <td>: <?= $userData['department_name'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>SUB DEPT</td>
                    <td>: <?= $userData['sub_department_name'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>LOKASI</td>
                    <td>: Menara Thamrin, Jakarta Pusat</td>
                </tr>
                <tr>
                    <td>PERIODE</td>
                    <td>: <?= $selectedMonth ?></td>
                </tr>
            </table>
        </div>

        <table class="overtime-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 10%;">Hari</th>
                    <th style="width: 10%;">Jam Masuk</th>
                    <th style="width: 10%;">Jam Keluar</th>
                    <th style="width: 10%;">Total Lembur</th>
                    <th style="width: 20%;">Deskripsi Lembur</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mengurutkan array $absensi berdasarkan tanggal
                usort($absensi, function ($a, $b) {
                    return strtotime($a['tanggal']) - strtotime($b['tanggal']);
                });

                $totalHours = 0;
                foreach ($absensi as $index => $item):
                    if ($item['jam_masuk'] && $item['jam_keluar']) {
                        $tanggal_keluar = !empty($item['tanggal_keluar']) ? $item['tanggal_keluar'] : $item['tanggal'];
                        $masuk = strtotime($item['tanggal'] . ' ' . $item['jam_masuk']);
                        $keluar = strtotime($tanggal_keluar . ' ' . $item['jam_keluar']);
                        $diffMinutes = ($keluar - $masuk) / 60;
                        $hours = floor($diffMinutes / 60);
                        $minutes = $diffMinutes % 60;
                        $totalHoursItem = sprintf('%d jam %02d menit', $hours, $minutes);
                        $totalHours += $diffMinutes / 60;
                    } else {
                        $totalHoursItem = '-';
                    }
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                        <td><?= date('l', strtotime($item['tanggal'])) ?></td>
                        <td><?= $item['jam_masuk'] ? date('H:i', strtotime($item['jam_masuk'])) : '-' ?></td>
                        <td><?= $item['jam_keluar'] ? date('H:i', strtotime($item['jam_keluar'])) : '-' ?></td>
                        <td><?= $totalHoursItem ?></td>
                        <td class="description"><?= $item['kegiatan_harian'] ?? '-' ?> (#<?= $item['no_tiket'] ?>)</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>Total Jam Lembur</strong></td>
                    <td colspan="2"><strong><?= sprintf('%.2f jam', $totalHours) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <div class="signature-box-left">
                <p>Menyetujui,</p>
                <div class="signature-space"></div>
                <p><u>RAHARDIKA NUR PERMANA</u><br>
                    NIK: 92161515</p>
            </div>
            <div class="signature-box-right">
                <p>Dibuat Oleh,</p>
                <?php if (isset($signature_path) && $signature_path): ?>
                    <div class="signature-img">
                        <img src="<?= $signature_path ?>" style="max-width: 100px; max-height: 50px;">
                    </div>
                <?php else: ?>
                    <div class="signature-space" style="height: 30px;"></div>
                <?php endif; ?>
                <p><u><?= user()->username ?></u><br>
                    NIK: <?= user()->nik ?? '-' ?></p>
            </div>
        </div>
    </div>
    <!-- Individual Overtime Forms -->
    <?php foreach ($absensi as $index => $item):
        if ($item['jam_masuk'] && $item['jam_keluar']): // Only create forms for valid entries
    ?>
            <div class="page-break" style="margin: 0; padding: 0;">
                <div class="container" style="max-width: 100%; padding: 5px; font-size: 12px; line-height: 0.7;">
                    <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 5px;">
                        <div class="form-section" style="font-size: 12px;">Form lembur karyawan Non shift</div>
                        <img src="<?= $logo_path ?>" alt="Logo Lintas" class="logo" style="width: 70px; height: 40px; object-fit: cover;">
                        <div class="header-text" style="font-size: 16px;"><u>SURAT TUGAS LEMBUR</u></div>
                    </div>
                    <div class="form-section" style="font-size: 12px; line-height: 0.7;">
                        <p style="margin: 0;">Di instruksikan kepada :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Nama</td>
                                <td style="padding: 2px;">: <?= user()->username ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">NIK</td>
                                <td style="padding: 2px;">: <?= $item['nik'] ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Bagian/Divisi</td>
                                <td style="padding: 2px;">: <?= $userData['department_name'] ?? '-' ?> / <?= $userData['division_name'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Lokasi Kerja</td>
                                <td style="padding: 2px;">: Menara Thamrin, Jakarta Pusat</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Pemberi Tugas</td>
                                <td style="padding: 2px;">: <?= $item['pbr_tugas'] ?></td>
                            </tr>
                        </table>

                        <p>Untuk melaksanakan lembur pada :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Hari/Tanggal</td>
                                <td style="padding: 2px;">: <?= date('l / d F Y', strtotime($item['tanggal'])) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Jam</td>
                                <td style="padding: 2px;">: <?= date('H:i', strtotime($item['jam_masuk'])) ?> s.d <?= date('H:i', strtotime($item['jam_keluar'])) ?></td>
                            </tr>
                        </table>

                        <p>Pelaksanaan Lembur tersebut di perlukan untuk menyelesaikan tugas sebagai berikut :</p>
                        <p style="margin: 5px 0;">
                            <strong><?= $item['kegiatan_harian'] ?></strong> (<strong>#<?= $item['no_tiket'] ?></strong>)
                        </p>

                        <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 20px;">
                            <div class="signature-box-left" style="width: 45%; font-size: 12px;">
                                <p>Menyetujui,</p>
                                <div class="signature-space" style="height: 60px;"></div>
                                <p><u>RAHARDIKA NUR PERMANA</u><br><br>NIK: 92161515</p>
                            </div>
                            <div class="signature-box-right" style="width: 45%; font-size: 12px;">
                                <div class="date">Jakarta, <?= date('d F Y', strtotime($item['tanggal'])) ?></div>
                                <p>Yang di beri tugas,</p>
                                <?php if (isset($signature_path) && $signature_path): ?>
                                    <div class="signature-img">
                                        <img src="<?= $signature_path ?>" style="max-width: 100px; max-height: 50px;">
                                    </div>
                                <?php else: ?>
                                    <div class="signature-space" style="height: 30px;"></div>
                                <?php endif; ?>
                                <p><u><?= user()->username ?></u><br><br>NIK: <?= $item['nik'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="title" style="font-size: 16px; margin-top: 40px; margin-bottom: 10px;"><u>LAPORAN PELAKSANAAN LEMBUR</u></div>

                    <div class="form-section" style="font-size: 12px; line-height: 0.7;">
                        <p>Berdasarkan Surat Tugas Lembur No : ................................ yang bertanda tangan di bawah ini :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Nama</td>
                                <td style="padding: 2px;">: <?= user()->username ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">NIK</td>
                                <td style="padding: 2px;">: <?= $item['nik'] ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Bagian/Divisi</td>
                                <td style="padding: 2px;">: <?= $userData['department_name'] ?? '-' ?> / <?= $userData['division_name'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Lokasi Kerja</td>
                                <td style="padding: 2px;">: Menara Thamrin, Jakarta Pusat</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Pemberi Tugas</td>
                                <td style="padding: 2px;">: <?= $item['pbr_tugas'] ?></td>
                            </tr>
                        </table>

                        <p>Telah melaksanakan lembur pada :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Hari/Tanggal</td>
                                <td style="padding: 2px;">: <?= date('l / d F Y', strtotime($item['tanggal'])) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Jam</td>
                                <td style="padding: 2px;">: <?= date('H:i', strtotime($item['jam_masuk'])) ?> s.d <?= date('H:i', strtotime($item['jam_keluar'])) ?></td>
                            </tr>
                        </table>

                        <p>Pelaksanaan Lembur tersebut di perlukan untuk menyelesaikan tugas sebagai berikut :</p>
                        <p style="margin: 5px 0;">
                            <strong><?= $item['kegiatan_harian'] ?></strong> (<strong>#<?= $item['no_tiket'] ?></strong>)
                        </p>

                        <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 20px;">
                            <div class="signature-box-left" style="width: 45%; font-size: 12px;">
                                <p>Menyetujui,</p>
                                <div class="signature-space" style="height: 60px;"></div>
                                <p><u>RAHARDIKA NUR PERMANA</u><br><br>NIK: 92161515</p>
                            </div>
                            <div class="signature-box-right" style="width: 45%; font-size: 12px;">
                                <div class="date">Jakarta, <?= date('d F Y', strtotime($item['tanggal'])) ?></div>
                                <p>Yang di beri tugas,</p>
                                <?php if (isset($signature_path) && $signature_path): ?>
                                    <div class="signature-img">
                                        <img src="<?= $signature_path ?>" style="max-width: 100px; max-height: 50px;">
                                    </div>
                                <?php else: ?>
                                    <div class="signature-space" style="height: 30px;"></div>
                                <?php endif; ?>
                                <p><u><?= user()->username ?></u><br><br>NIK: <?= $item['nik'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        endif;
    endforeach;
    ?>

</body>

</html>