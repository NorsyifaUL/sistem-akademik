<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekap Bulanan Absensi - Sman 1 Jejangkit</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #333; line-height: 1.2; margin: 0; padding: 0; }
        
        /* Header yang sudah disesuaikan tanpa logo */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px double #000; margin-bottom: 15px; text-align: center; }
        .header-table td { vertical-align: middle; padding-bottom: 10px; }
        .text-cell h1 { margin: 0; font-size: 15px; text-transform: uppercase; font-weight: normal; }
        .text-cell .school-name { font-size: 20px; font-weight: bold; margin: 2px 0; display: block; text-transform: uppercase; }

        .info-table { width: 100%; margin-bottom: 10px; }
        .info-label { font-weight: bold; text-transform: uppercase; font-size: 8px; width: 80px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th { background-color: #f2f2f2; border: 1px solid #000; padding: 4px 2px; text-align: center; font-weight: bold; }
        .main-table td { border: 1px solid #000; padding: 4px 2px; text-align: center; }
        .col-nama { text-align: left !important; padding-left: 8px !important; text-transform: uppercase; font-weight: bold; }
        .text-red { color: #8b0000; font-weight: bold; }
        .footer { margin-top: 25px; width: 100%; }
        .signature-box { float: right; width: 220px; text-align: center; }
        .signature-box p { margin: 0; padding: 0; }
        .signature-box .place-date { margin-bottom: 2px; }
        .signature-box .role-title { font-weight: bold; margin-top: 1px; }
        .signature-space { height: 55px; }
        .signature-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .signature-nip { margin-top: 1px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="text-cell">
                <h1>Laporan Rekapitulasi Absensi Bulanan Siswa</h1>
                <span class="school-name">SMA Negeri 1 Jejangkit</span>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="info-label">Bulan/Tahun</td>
            <td>: {{ strtoupper($bulan_teks) }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td class="info-label">Kelas</td>
            <td>: {{ strtoupper($kelas) }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="25">NO</th>
                <th rowspan="2">NAMA LENGKAP SISWA</th>
                <th colspan="{{ $jumlah_hari }}">TANGGAL</th>
                <th colspan="4">TOTAL</th> 
            </tr>
            <tr>
                @for($i = 1; $i <= $jumlah_hari; $i++)
                    <th width="12">{{ $i }}</th>
                @endfor
                <th width="15">H</th> 
                <th width="15">S</th>
                <th width="15">I</th>
                <th width="15">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="col-nama">{{ $row['nama'] }}</td>
                
                @for($i = 1; $i <= $jumlah_hari; $i++)
                    @php 
                        $st = $row['hari'][$i];
                        $class = ($st == 'A') ? 'text-red' : '';
                    @endphp
                    <td class="{{ $class }}">
                        {{ $st == '.' ? '' : $st }}
                    </td>
                @endfor

                <td style="font-weight: bold;">{{ $row['total']['H'] }}</td> 
                <td>{{ $row['total']['S'] }}</td>
                <td>{{ $row['total']['I'] }}</td>
                <td class="text-red">{{ $row['total']['A'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p class="place-date">Jejangkit, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <p class="role-title">Kepala Sekolah</p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $setting->nama_kepsek ?? '..........................' }}</p>
            <p class="signature-nip">NIP. {{ $setting->nip_kepsek ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>