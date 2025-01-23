<!DOCTYPE html>
<html>

<head>
    <title>Activity Report</title>
    <style>
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
        }

        table,
        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 9pt;
        }

        .table-header {
            background-color: #f4801e;
            font-weight: bold;
            text-align: center;
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
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .data-row:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="company-name">PT. APLIKANUSA LINTASARTA</h1>
        <p class="company-address">
            Jakarta Pusat, Menara Thamrin 12th Floor<br>
            Jl. M.H. Thamrin Kav.3 Jakarta 10250<br>
            Telepon: +6221 230 2345 | Email: info@lintasarta.co.id
        </p>
        <hr>
        <h2 class="report-title">ACTIVITY REPORT</h2>
    </div>

    <div class="report-info">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 30%;">Periode</td>
                <td style="border: none;">: <?= $selectedMonth ?></td>
            </tr>
            <tr>
                <td style="border: none;">Print Date</td>
                <td style="border: none;">: <?= date('d F Y') ?></td>
            </tr>
            <tr>
                <td style="border: none;">Name</td>
                <td style="border: none;">: <?= $username ?></td>
            </tr>
            <tr>
                <td style="border: none;">Total Activities</td>
                <td style="border: none;">: <?= $totalActivities ?></td>
            </tr>
        </table>
    </div>

    <?php if (empty($activities)): ?>
        <div class="no-data">
            No activities found for the selected period.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr class="table-header">
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 20%;">Task</th>
                    <th style="width: 23%;">Description</th>
                    <th style="width: 15%;">Location</th>
                    <th style="width: 12%;">Start Time</th>
                    <th style="width: 12%;">End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $index => $activity): ?>
                    <tr class="data-row">
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($activity['activity_date'])) ?></td>
                        <td><?= esc($activity['task']) ?></td>
                        <td><?= esc($activity['description']) ?></td>
                        <td><?= esc($activity['location']) ?></td>
                        <td class="text-center"><?= date('H:i', strtotime($activity['start_time'])) ?></td>
                        <td class="text-center"><?= date('H:i', strtotime($activity['end_time'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="signature">
            <p>
                Jakarta, <?= date('d F Y') ?><br>
                Mengetahui,<br><br><br><br>
                <br>
                <?= $username ?><br>
                NIK: <?= user()->nik ?? '-' ?>
            </p>
        </div>
    <?php endif; ?>
</body>

</html>