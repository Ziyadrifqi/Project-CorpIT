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
            width: 60%;
        }

        .employee-info td {
            padding: 5px;
            border: none;
        }

        .employee-info td:first-child {
            width: 120px;
        }

        .overtime-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: small;
        }

        .overtime-table th,
        .overtime-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
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
            height: 60px;
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
            <img src="<?= $logo_path ?>" alt="Logo Lintas" class="logo">
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
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Total Lembur</th>
                    <th>Deskripsi Lembur</th>
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
                    <td colspan="4" style="text-align: right;"><strong>Total Jam Lembur</strong></td>
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
                <div class="signature-space"></div>
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
                <div class="container" style="max-width: 100%; padding: 5px; font-size: 12px; line-height: 1.5;">
                    <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 5px;">
                        <div class="form-section" style="font-size: 12px;">Form lembur karyawan Non shift</div>
                        <img src="<?= $logo_path ?>" alt="Logo Lintas" class="logo">
                        <div class="header-text" style="font-size: 16px;">SURAT TUGAS LEMBUR</div>
                    </div>
                    <div class="form-section" style="font-size: 12px; line-height: 1;">
                        <p style="margin: 0;">Di instruksikan kepada :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Nama</td>
                                <td style="padding: 2px;">: <?= user()->username ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">NIK</td>
                                <td style="padding: 2px;">: <?= user()->nik ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Bagian/Divisi</td>
                                <td style="padding: 2px;">: <?= $userData['department_name'] ?? '-' ?> / <?= $userData['division_name'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Lokasi Kerja</td>
                                <td style="padding: 2px;">: Menara Thamrin, Jakarta Pusat</td>
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
                                <div class="signature-space" style="height: 30px;"></div>
                                <p><u>RAHARDIKA NUR PERMANA</u><br>NIK: 92161515</p>
                            </div>
                            <div class="signature-box-right" style="width: 45%; font-size: 12px;">
                                <div class="date">Jakarta, <?= date('d F Y', strtotime($item['tanggal'])) ?></div>
                                <p>Yang di beri tugas,</p>
                                <div class="signature-space" style="height: 30px;"></div>
                                <p><u><?= user()->username ?></u><br>NIK: <?= user()->nik ?? '-' ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="title" style="font-size: 16px; margin-top: 40px; margin-bottom: 10px;">LAPORAN PELAKSANAAN LEMBUR</div>

                    <div class="form-section" style="font-size: 12px; line-height: 0.9;">
                        <p>Berdasarkan Surat Tugas Lembur No : ................................ yang bertanda tangan di bawah ini :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Nama</td>
                                <td style="padding: 2px;">: <?= user()->username ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">NIK</td>
                                <td style="padding: 2px;">: <?= user()->nik ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Bagian/Divisi</td>
                                <td style="padding: 2px;">: <?= $userData['department_name'] ?? '-' ?> / <?= $userData['division_name'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Lokasi Kerja</td>
                                <td style="padding: 2px;">: Menara Thamrin, Jakarta Pusat</td>
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
                                <div class="signature-space" style="height: 30px;"></div>
                                <p><u>RAHARDIKA NUR PERMANA</u><br>NIK: 92161515</p>
                            </div>
                            <div class="signature-box-right" style="width: 45%; font-size: 12px;">
                                <div class="date">Jakarta, <?= date('d F Y', strtotime($item['tanggal'])) ?></div>
                                <p>Yang di beri tugas,</p>
                                <div class="signature-space" style="height: 30px;"></div>
                                <p><u><?= user()->username ?></u><br>NIK: <?= user()->nik ?? '-' ?></p>
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