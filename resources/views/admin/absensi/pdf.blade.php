<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - Sman 1 Jejangkit</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            position: relative;
        }
        .header h1 {
            text-transform: uppercase;
            font-size: 14px;
            margin: 0;
            color: #000;
        }
        .header .school-name {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
            display: block;
            text-transform: uppercase;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            max-height: 70px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            width: 100px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .main-table td {
            border: 1px solid #000;
            padding: 7px 6px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .status {
            font-weight: bold;
            font-size: 8px;
            padding: 2px 4px;
            border-radius: 3px;
            text-align: center;
            display: block;
        }
        .status-h { color: #006400; background-color: #e6ffed; border: 0.5px solid #006400; }
        .status-s { color: #8b4513; background-color: #fff9e6; border: 0.5px solid #8b4513; }
        .status-i { color: #00008b; background-color: #e6f0ff; border: 0.5px solid #00008b; }
        .status-a { color: #8b0000; background-color: #ffe6e6; border: 0.5px solid #8b0000; }

        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="header">
        @if(!empty($info['logo']))
            <img src="{{ public_path('storage/' . $info['logo']) }}" class="logo">
        @endif

        <h1>Laporan Monitoring Absensi Siswa</h1>
        {{-- Nama sekolah diisi manual sesuai instruksi --}}
        <span class="school-name">Sman 1 Jejangkit</span>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Periode</td>
            <td width="200">: {{ $info['mode'] == 'bulanan' ? 'REKAP BULANAN' : 'HARIAN' }}</td>
            <td class="info-label">Dicetak Pada</td>
            <td>: {{ date('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="info-label">{{ $info['mode'] == 'bulanan' ? 'Bulan' : 'Tanggal' }}</td>
            <td>: {{ $info['mode'] == 'bulanan' ? $info['bulan'] : date('d F Y', strtotime($info['tanggal'])) }}</td>
            <td class="info-label">Kelas</td>
            <td>: {{ $info['kelas'] }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="90">NISN</th>
                <th>Nama Siswa</th>
                <th width="80">Kelas</th>
                <th width="65">Status</th>
                <th width="110">Waktu Presensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $key => $a)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">{{ $a->siswa->nisn ?? '-' }}</td>
                <td class="font-bold">{{ strtoupper($a->siswa->nama ?? 'N/A') }}</td>
                <td class="text-center">{{ $a->siswa->dataKelas->nama_kelas ?? 'N/A' }}</td>
                <td class="text-center">
                    @php
                        $s = strtoupper($a->status);
                        $labels = ['H' => 'HADIR', 'S' => 'SAKIT', 'I' => 'IZIN', 'A' => 'ALFA'];
                    @endphp
                    <span class="status status-{{ strtolower($s) }}">
                        {{ $labels[$s] ?? $s }}
                    </span>
                </td>
                <td class="text-center">
                    {{ $a->created_at->format('H:i') }} 
                    <span style="color: #777; font-size: 9px;">({{ $a->created_at->format('d/m/y') }})</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Data absensi tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Jejangkit, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <p class="font-bold">Kepala Sekolah</p>
            
            <div class="signature-space"></div>
            
            <p class="font-bold" style="text-decoration: underline; margin-bottom: 2px;">
                {{ $info['kepala_sekolah'] }}
            </p>
            <p style="margin-top: 0;">NIP. {{ $info['nip'] }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>