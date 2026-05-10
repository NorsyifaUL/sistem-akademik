<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekap Bulanan Absensi - Sman 1 Jejangkit</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px; 
            color: #333;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            position: relative;
        }
        .header h1 {
            text-transform: uppercase;
            font-size: 12px;
            margin: 0;
        }
        .header .school-name {
            font-size: 16px;
            font-weight: bold;
            margin: 2px 0;
            display: block;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            width: 80px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-weight: bold;
        }
        .main-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }
        .text-left { text-align: left; padding-left: 5px !important; }
        .font-bold { font-weight: bold; }
        .text-red { color: #8b0000; font-weight: bold; }
        
        .footer {
            margin-top: 20px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-space { height: 40px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Rekapitulasi Absensi Bulanan Siswa</h1>
        <span class="school-name">Sman 1 Jejangkit</span>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Bulan/Tahun</td>
            <td>: {{ $bulan_teks }} {{ $tahun }}</td>
            <td class="info-label">Kelas</td>
            <td>: {{ $kelas }}</td>
        </tr>
        <tr>
            <td class="info-label">Dicetak Pada</td>
            <td colspan="3">: {{ date('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="20">No</th>
                <th rowspan="2">Nama Siswa</th>
                <th colspan="{{ $jumlah_hari }}">Tanggal</th>
                <th colspan="4">Total</th> </tr>
            <tr>
                @for($i = 1; $i <= $jumlah_hari; $i++)
                    <th width="15">{{ $i }}</th>
                @endfor
                <th width="15">H</th> <th width="15">S</th>
                <th width="15">I</th>
                <th width="15">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="text-left font-bold">{{ strtoupper($row['nama']) }}</td>
                @for($i = 1; $i <= $jumlah_hari; $i++)
                    @php 
                        $st = $row['hari'][$i];
                        $class = ($st == 'A') ? 'text-red' : '';
                    @endphp
                    <td class="{{ $class }}">
                        {{ $st == '.' ? '' : $st }}
                    </td>
                @endfor
                <td>{{ $row['total']['H'] }}</td> <td>{{ $row['total']['S'] }}</td>
                <td>{{ $row['total']['I'] }}</td>
                <td class="text-red">{{ $row['total']['A'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Jejangkit, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <p class="font-bold">Kepala Sekolah</p>
            <div class="signature-space"></div>
            <p class="font-bold" style="text-decoration: underline; margin-bottom: 2px;">
                {{ $setting->nama_kepsek ?? '..........................' }}
            </p>
            <p style="margin-top: 0;">NIP. {{ $setting->nip_kepsek ?? '-' }}</p>
        </div>
    </div>

</body>
</html>