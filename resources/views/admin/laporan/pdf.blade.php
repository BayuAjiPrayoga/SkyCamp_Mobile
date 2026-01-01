<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan - {{ $rangeString }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2D5016;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2D5016;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            color: #6b7280;
            margin: 5px 0 0;
        }

        .section-title {
            color: #2D5016;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th {
            background-color: #2D5016;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .total-row {
            font-weight: bold;
            background-color: #d1fae5 !important;
        }

        .total-row td {
            border-top: 2px solid #2D5016;
        }

        .summary-box {
            background-color: #f0fdf4;
            border: 1px solid #2D5016;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        .summary-box h3 {
            color: #2D5016;
            margin: 0 0 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🏔️ LuhurCamp</h1>
        <p>Laporan Pendapatan - {{ $rangeString }}</p>
    </div>

    <div class="section-title">Rincian Pendapatan Mingguan</div>

    <table>
        <thead>
            <tr>
                <th>Minggu</th>
                <th>Periode</th>
                <th class="text-right">Jumlah Booking</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyData as $week)
                <tr>
                    <td>Minggu {{ $week['week'] }}</td>
                    <td>{{ $week['start'] }} - {{ $week['end'] }}</td>
                    <td class="text-right">{{ $week['bookings'] }} booking</td>
                    <td class="text-right">Rp {{ number_format($week['revenue'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ $totalBookings }} booking</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <h3>📊 Ringkasan</h3>
        <table style="border: none;">
            <tr style="background: transparent;">
                <td style="border: none;">Total Booking Dikonfirmasi:</td>
                <td style="border: none; text-align: right;"><strong>{{ $totalBookings }}</strong></td>
            </tr>
            <tr style="background: transparent;">
                <td style="border: none;">Total Pendapatan:</td>
                <td style="border: none; text-align: right;"><strong>Rp
                        {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
            </tr>
            <tr style="background: transparent;">
                <td style="border: none;">Rata-rata per Booking:</td>
                <td style="border: none; text-align: right;"><strong>Rp
                        {{ $totalBookings > 0 ? number_format($totalRevenue / $totalBookings, 0, ',', '.') : 0 }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini di-generate secara otomatis pada {{ now()->translatedFormat('d F Y H:i') }}</p>
        <p>© {{ date('Y') }} LuhurCamp - Smart Camping in the Clouds</p>
    </div>
</body>

</html>