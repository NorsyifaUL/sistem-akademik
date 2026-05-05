<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Raport_{{ $siswa->nama }}</title>
    <style>
        @page { 
            size: a4; /* Menetapkan ukuran kertas ke A4 */
            margin: 0.8cm 1.2cm 1.5cm 1.2cm; 
        }

        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            color: #000; 
            line-height: 1.4; 
            /* Jarak agar konten tidak tertutup header */
            margin-top: 95px; 
            margin-bottom: 50px; 
        }
        
        header {
            position: fixed;
            top: -0.2cm;
            left: 0;
            right: 0;
            height: 75px; 
            background-color: white;
            z-index: 1000;
        }

        footer {
            position: fixed;
            bottom: -0.5cm;
            left: 0;
            right: 0;
            height: 40px;
            background-color: white;
        }

        .header-table, .footer-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; padding: 1px 0; font-size: 11px; }
        
        .header-line { border: none; border-top: 1.5px solid #000; margin: 4px 0 1px 0; }
        .footer-line { border: none; border-top: 1.5px solid #000; margin: 5px 0; }

        .page-number:before { content: counter(page); }

        .info-label { width: 18%; }
        .info-separator { width: 2%; text-align: center; }
        .info-value { width: 40%; font-weight: bold; }
        .info-label-right { width: 18%; }
        .info-value-right { width: 22%; font-weight: bold; }

        .title { text-align: center; font-size: 14px; font-weight: bold; margin-top: 0; margin-bottom: 15px; text-transform: uppercase; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 6px; }
        .main-table th { text-align: center; font-weight: bold; text-transform: uppercase; font-size: 10px; background-color: #f2f2f2; }
        
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }

        .category-row { font-weight: bold; background-color: #f9f9f9; padding: 5px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }
        .narasi { text-align: justify; font-size: 11px; line-height: 1.4; padding: 5px 8px; word-wrap: break-word; }
        
        .catatan-box { border: 1px solid #000; padding: 10px; min-height: 40px; margin-top: 5px; margin-bottom: 15px; text-align: justify; width: 100%; box-sizing: border-box; font-size: 11px; }
        
        .footer-ttd { width: 100%; border-collapse: collapse; table-layout: fixed; border: none; margin-top: 10px; }
        .footer-ttd td { text-align: center; vertical-align: top; border: none; font-size: 11px; }
        .ttd-space { height: 50px; } 
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td class="info-label">Nama Peserta Didik</td><td class="info-separator">:</td><td class="info-value uppercase">{{ $siswa->nama }}</td>
                <td class="info-label-right">Kelas</td><td class="info-separator">:</td><td class="info-value-right">{{ $siswa->kelas }}</td>
            </tr>
            <tr>
                <td class="info-label">NISN</td><td class="info-separator">:</td><td class="info-value">{{ $siswa->nisn }}</td>
                <td class="info-label-right">Fase</td><td class="info-separator">:</td><td class="info-value-right">E</td>
            </tr>
            <tr>
                <td class="info-label">Sekolah</td><td class="info-separator">:</td><td class="info-value uppercase">SMAN 1 Jejangkit</td>
                <td class="info-label-right">Semester</td><td class="info-separator">:</td><td class="info-value-right">{{ $setting->semester }}</td>
            </tr>
            <tr>
                <td class="info-label">Alamat</td><td class="info-separator">:</td><td class="info-value">Jl. AMD Jejangkit Pasar</td>
                <td class="info-label-right">Tahun Pelajaran</td><td class="info-separator">:</td><td class="info-value-right">{{ $setting->tahun_ajaran }}</td>
            </tr>
        </table>
        <hr class="header-line">
    </header>

    <footer>
        <hr class="footer-line">
        <table class="footer-table">
            <tr>
                <td style="width: 70%; text-align: left; font-size: 10px;">
                    {{ $siswa->kelas }} | {{ $siswa->nama }} | {{ $siswa->nisn }}
                </td>
                <td style="width: 30%; text-align: right; font-size: 10px;">
                    Halaman : <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <div class="title">LAPORAN HASIL BELAJAR</div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="25%">MATA PELAJARAN</th>
                    <th width="12%">NILAI AKHIR</th>
                    <th width="58%">CAPAIAN KOMPETENSI</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="4" class="category-row">Kelompok Umum</td></tr>
                @forelse($dataRaport as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $row['mapel'] }}</td>
                    <td class="center bold">{{ $row['akhir'] }}</td>
                    <td class="narasi">{{ $row['capaian_kompetensi'] }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="center italic">Data nilai tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="30%">KEGIATAN EKSTRAKURIKULER</th>
                    <th width="15%">PREDIKAT</th>
                    <th width="50%">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eskul as $index => $ex)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="uppercase bold">{{ $ex['kegiatan'] }}</td>
                    <td class="center bold">{{ $ex['nilai'] }}</td>
                    <td class="narasi">{{ $ex['keterangan'] }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="center italic">- Tidak ada data ekstrakurikuler -</td></tr>
                @endforelse
            </tbody>
        </table>

        <table class="main-table" style="width: 45%;">
            <tbody>
                <tr><td width="60%">Sakit</td><td class="center bold">{{ $absensi['sakit'] ?? 0 }} hari</td></tr>
                <tr><td>Izin</td><td class="center bold">{{ $absensi['izin'] ?? 0 }} hari</td></tr>
                <tr><td>Tanpa Keterangan</td><td class="center bold">{{ $absensi['alfa'] ?? 0 }} hari</td></tr>
            </tbody>
        </table>

        <div style="font-weight: bold; font-size: 12px; margin-bottom: 5px;">CATATAN WALI KELAS</div>
        <div class="catatan-box">
            <span class="bold">{{ $catatan_wali }}</span>
        </div>

        <table style="width: 100%; border: none; margin-top: 10px;">
            <tr>
                <td width="66%"></td> 
                <td width="34%" style="text-align: center; font-size: 11px;">
                    Jejangkit, {{ \Carbon\Carbon::parse($setting->tgl_raport)->translatedFormat('d F Y') }}
                </td>
            </tr>
        </table>

        <table class="footer-ttd">
            <tr>
                <td width="33%">Mengetahui,<br>Orang Tua/Wali<div class="ttd-space"></div>( .................................... )</td>
                <td width="34%">Mengetahui,<br>Kepala Sekolah<div class="ttd-space"></div><strong><u>{{ $setting->nama_kepsek }}</u></strong><br>NIP. {{ $setting->nip_kepsek }}</td>
                <td width="33%">
                    <br> <!-- Penyeimbang baris -->
                    Wali Kelas<div class="ttd-space"></div>
                    <strong><u>{{ $nama_wali }}</u></strong><br>NIP. {{ $nip }}
                </td>
            </tr>
        </table>
    </main>

</body>
</html>