<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Raport_{{ $siswa->nama }}</title>
    <style>
        @page { margin: 0.8cm 1.2cm 0.8cm 1.2cm; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; line-height: 1.3; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-table td { vertical-align: top; padding: 2px 0; }
        .info-label { width: 18%; }
        .info-separator { width: 2%; text-align: center; }
        .info-value { width: 40%; font-weight: bold; }
        .info-label-right { width: 18%; }
        .info-value-right { width: 22%; font-weight: bold; }
        .title { text-align: center; font-size: 13px; font-weight: bold; margin: 12px 0; text-transform: uppercase; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 5px; }
        .main-table th { text-align: center; font-weight: bold; text-transform: uppercase; font-size: 9px; background-color: #f2f2f2; }
        .category-row { font-weight: bold; background-color: #f9f9f9; padding: 5px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .narasi { text-align: justify; font-size: 9px; line-height: 1.4; padding: 5px 8px; word-wrap: break-word; }
        .catatan-box { border: 1px solid #000; padding: 10px; min-height: 45px; margin-top: 5px; margin-bottom: 15px; text-align: justify; }
        .footer-ttd { width: 100%; border-collapse: collapse; table-layout: fixed; border: none; margin-top: 10px; }
        .footer-ttd td { text-align: center; vertical-align: top; border: none; }
        .ttd-space { height: 50px; } 
        .uppercase { text-transform: uppercase; }
        .mt-10 { margin-top: 10px; }
        .italic { font-style: italic; }
    </style>
</head>
<body>

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
            <td class="info-label-right">Semester</td><td class="info-separator">:</td><td class="info-value-right">{{ $semester }}</td>
        </tr>
        <tr>
            <td class="info-label">Alamat</td><td class="info-separator">:</td><td class="info-value">Jl. AMD Jejangkit Pasar</td>
            <td class="info-label-right">Tahun Pelajaran</td><td class="info-separator">:</td><td class="info-value-right">{{ $tahun_ajaran }}</td>
        </tr>
    </table>

    <div class="title">LAPORAN HASIL BELAJAR</div>

    {{-- A. NILAI AKADEMIK --}}
    <div class="bold uppercase" style="font-size: 11px; margin-bottom: 5px;">A. NILAI AKADEMIK</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="25%">MATA PELAJARAN</th>
                <th width="10%">NILAI AKHIR</th>
                <th width="60%">CAPAIAN KOMPETENSI</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="4" class="category-row">Kelompok Umum</td></tr>
            @forelse($dataRaport as $index => $row)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $row['mapel'] }}</td>
                <td class="center bold">{{ $row['akhir'] }}</td>
                <td class="narasi">
                    {{ $row['capaian_kompetensi'] }}
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="center italic">Data nilai tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- B. EKSTRAKURIKULER --}}
    <div class="bold uppercase mt-10" style="font-size: 11px; margin-bottom: 5px;">B. KEGIATAN EKSTRAKURIKULER</div>
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
                {{-- Memanggil key 'kegiatan' dari array yang kita buat di Controller --}}
                <td class="uppercase bold">{{ $ex['kegiatan'] }}</td>
                <td class="center bold">{{ $ex['nilai'] }}</td>
                <td class="narasi">
                    {{ $ex['keterangan'] }}
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="center italic">- Tidak ada data ekstrakurikuler -</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- C. KETIDAKHADIRAN & CATATAN --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td width="45%" style="vertical-align: top;">
                <div class="bold uppercase" style="font-size: 11px; margin-bottom: 5px;">C. KETIDAKHADIRAN</div>
                <table class="main-table" style="width: 100%;">
                    <tbody>
                        <tr><td width="60%">Sakit</td><td class="center bold">{{ $absensi['sakit'] ?? 0 }} hari</td></tr>
                        <tr><td>Izin</td><td class="center bold">{{ $absensi['izin'] ?? 0 }} hari</td></tr>
                        <tr><td>Tanpa Keterangan</td><td class="center bold">{{ $absensi['alfa'] ?? 0 }} hari</td></tr>
                    </tbody>
                </table>
            </td>
            <td width="5%"></td>
            <td width="50%" style="vertical-align: top;">
                <div class="bold uppercase" style="font-size: 11px; margin-bottom: 5px;">D. CATATAN WALI KELAS</div>
                <div class="catatan-box">
                    {{ $catatan_wali }}
                </div>
            </td>
        </tr>
    </table>

    {{-- TANDA TANGAN --}}
    <table style="width: 100%; border: none; margin-top: 25px;">
        <tr>
            <td width="66%"></td> 
            <td width="34%" style="text-align: center; font-size: 10px;">
                {{ $setting->tempat ?? 'Jejangkit' }}, 
                {{ $setting->tanggal_raport ? \Carbon\Carbon::parse($setting->tanggal_raport)->translatedFormat('d F Y') : date('d F Y') }}
            </td>
        </tr>
    </table>

    <table class="footer-ttd">
        <tr>
            <td width="33%">
                Mengetahui,<br>Orang Tua/Wali
                <div class="ttd-space"></div>
                ( .................................... )
            </td>
            <td width="34%">
                Mengetahui,<br>Kepala Sekolah
                <div class="ttd-space"></div>
                <strong><u>{{ $nama_kepsek }}</u></strong><br>
                NIP. {{ $nip_kepsek }}
            </td>
            <td width="33%">
                Wali Kelas
                <div class="ttd-space"></div>
                <br>
                <strong><u>{{ $nama_wali }}</u></strong><br>
                NIP. {{ $nip }}
            </td>
        </tr>
    </table>

</body>
</html>