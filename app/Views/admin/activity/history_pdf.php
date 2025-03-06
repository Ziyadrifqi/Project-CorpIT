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

        .signature-img {
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .signature-img img {
            max-width: 100px;
            max-height: 50px;
            object-fit: contain;
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
                    <td>: <?= $userData['fullname'] ?></td>
                </tr>
                <tr>
                    <td>JABATAN</td>
                    <td>: <?= $userData['position'] ?? '-' ?></td>
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

        <?php if (empty($activities)): ?>
            <p>No activities found for the selected period.</p>
        <?php else: ?>
            <table class="overtime-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Total Lembur</th>
                        <th>Description</th>
                        <th>No Ticket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $index => $activity): ?>
                        <?php
                        $start = strtotime($activity['start_time']);
                        $end = strtotime($activity['end_time']);
                        $totalLembur = sprintf('%d jam %02d menit', floor(($end - $start) / 3600), ($end - $start) % 3600 / 60);
                        $hari = ["Sunday" => "Minggu", "Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jumat", "Saturday" => "Sabtu"];
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= date('d/m/Y', strtotime($activity['activity_date'])) ?></td>
                            <td><?= $hari[date('l', strtotime($activity['activity_date']))] ?></td>
                            <td><?= date('H:i', strtotime($activity['start_time'])) ?></td>
                            <td><?= date('H:i', strtotime($activity['end_time'])) ?></td>
                            <td><?= $totalLembur ?></td>
                            <td><?= esc($activity['description']) ?></td>
                            <td><?= esc($activity['no_tiket']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="signature-section">
            <div class="signature-box-left">
                <p>Menyetujui,</p>
                <?php if ($isSigned && isset($currentUser) && $currentUser->signature): ?>
                    <div class="signature-img">
                        <img src="<?= FCPATH . 'img/ttd/' . $currentUser->signature ?>" alt="Signature" style="max-width: 100px; max-height: 50px;">
                    </div>
                <?php else: ?>
                    <div class="signature-space" style="height: 30px;"></div>
                <?php endif; ?>
                <p><u><?= $currentUser->fullname ?></u><br>NIK: 92161515</p>
            </div>
            <div class="signature-box-right">
                <p>Dibuat Oleh,</p>
                <?php if (isset($userData['signature']) && $userData['signature']): ?>
                    <div class="signature-img">
                        <img src="<?= FCPATH . 'img/ttd/' . $userData['signature'] ?>" style="max-width: 100px; max-height: 50px;">
                    </div>
                <?php else: ?>
                    <div class="signature-space" style="height: 30px;"></div>
                <?php endif; ?>
                <p><u><?= esc($userData['fullname']) ?></u><br>NIK: <?= esc($activity['nik']) ?? '-' ?></p>
            </div>
        </div>
    </div>
    <!-- Individual Overtime Forms -->
    <?php foreach ($activities as $index => $item):
        if ($item['start_time'] && $item['end_time']): // Only create forms for valid entries
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
                                <td style="padding: 2px;">: <?= esc($activity['fullname']) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">NIK</td>
                                <td style="padding: 2px;">: <?= esc($activity['nik']) ?></td>
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
                                <td style="padding: 2px;">: <?= esc($item['pbr_tugas']) ?></td>
                            </tr>
                        </table>

                        <p>Untuk melaksanakan lembur pada :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Hari/Tanggal</td>
                                <td style="padding: 2px;">: <?= date('l / d F Y', strtotime($item['activity_date'])) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Jam</td>
                                <td style="padding: 2px;">: <?= date('H:i', strtotime($item['start_time'])) ?> s.d <?= date('H:i', strtotime($item['end_time'])) ?></td>
                            </tr>
                        </table>

                        <p>Pelaksanaan Lembur tersebut di perlukan untuk menyelesaikan tugas sebagai berikut :</p>
                        <p style="margin: 5px 0;">
                            <strong><?= $item['description'] ?></strong> (<strong>#<?= $item['no_tiket'] ?></strong>)
                        </p>

                        <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 20px;">
                            <div class="signature-box-left">
                                <p>Menyetujui,</p>
                                <?php if ($isSigned && isset($currentUser) && $currentUser->signature): ?>
                                    <div class="signature-img">
                                        <img src="<?= FCPATH . 'img/ttd/' . $currentUser->signature ?>" alt="Signature" style="max-width: 100px; max-height: 50px;">
                                    </div>
                                <?php else: ?>
                                    <div class="signature-space" style="height: 30px;"></div>
                                <?php endif; ?>
                                <p><u><?= $currentUser->fullname ?></u><br><br>NIK: 92161515</p>
                            </div>
                            <div class="signature-box-right">
                                <p>Dibuat Oleh,</p>
                                <?php if (isset($userData['signature']) && $userData['signature']): ?>
                                    <div class="signature-img">
                                        <img src="<?= FCPATH . 'img/ttd/' . $userData['signature'] ?>" style="max-width: 100px; max-height: 50px;">
                                    </div>
                                <?php else: ?>
                                    <div class="signature-space" style="height: 30px;"></div>
                                <?php endif; ?>
                                <p><u><?= esc($userData['fullname']) ?></u><br><br>NIK: <?= esc($activity['nik']) ?? '-' ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="title" style="font-size: 16px; margin-top: 40px; margin-bottom: 10px;"><u>LAPORAN PELAKSANAAN LEMBUR</u></div>

                    <div class="form-section" style="font-size: 12px; line-height: 0.7;">
                        <p>Berdasarkan Surat Tugas Lembur No : ................................ yang bertanda tangan di bawah ini :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Nama</td>
                                <td style="padding: 2px;">: <?= esc($activity['fullname']) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">NIK</td>
                                <td style="padding: 2px;">: <?= esc($activity['nik']) ?></td>
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
                                <td style="padding: 2px;">: <?= esc($item['pbr_tugas']) ?></td>
                            </tr>
                        </table>

                        <p>Telah melaksanakan lembur pada :</p>
                        <table class="employee-info" style="width: 100%; margin: 0; padding: 0; font-size: 12px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 2px;">Hari/Tanggal</td>
                                <td style="padding: 2px;">: <?= date('l / d F Y', strtotime($item['activity_date'])) ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;">Jam</td>
                                <td style="padding: 2px;">: <?= date('H:i', strtotime($item['start_time'])) ?> s.d <?= date('H:i', strtotime($item['end_time'])) ?></td>
                            </tr>
                        </table>

                        <p>Pelaksanaan Lembur tersebut di perlukan untuk menyelesaikan tugas sebagai berikut :</p>
                        <p style="margin: 5px 0;">
                            <strong><?= $item['description'] ?></strong> (<strong>#<?= $item['no_tiket'] ?></strong>)
                        </p>

                        <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 20px;">
                            <div class="signature-box-left">
                                <p>Menyetujui,</p>
                                <?php if ($isSigned && isset($currentUser) && $currentUser->signature): ?>
                                    <div class="signature-img">
                                        <img src="<?= FCPATH . 'img/ttd/' . $currentUser->signature ?>" alt="Signature" style="max-width: 100px; max-height: 50px;">
                                    </div>
                                <?php else: ?>
                                    <div class="signature-space" style="height: 30px;"></div>
                                <?php endif; ?>
                                <p><u><?= $currentUser->fullname ?></u><br><br>NIK: 92161515</p>
                            </div>
                            <div class="signature-box-right">
                                <p>Dibuat Oleh,</p>
                                <?php if (isset($userData['signature']) && $userData['signature']): ?>
                                    <div class="signature-img">
                                        <img src="<?= FCPATH . 'img/ttd/' . $userData['signature'] ?>" style="max-width: 100px; max-height: 50px;">
                                    </div>
                                <?php else: ?>
                                    <div class="signature-space" style="height: 30px;"></div>
                                <?php endif; ?>
                                <p><u><?= esc($userData['fullname']) ?></u><br><br>NIK: <?= esc($activity['nik']) ?? '-' ?></p>
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