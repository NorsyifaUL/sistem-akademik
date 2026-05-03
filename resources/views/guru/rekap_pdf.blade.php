<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai - {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; color: #333; margin: 20px; }
        
        /* KOP SURAT */
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 9pt; italic; }

        /* INFORMASI ATAS */
        .info-box { width: 100%; margin-bottom: 15px; }
        .info-box td { padding: 3px 0; vertical-align: top; }
        .label { font-weight: bold; width: 120px; }

        /* TABEL DATA */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { 
            background-color: #f2f2f2; 
            border: 1px solid #000; 
            padding: 10px; 
            text-align: center; 
            text-transform: uppercase;
            font-size: 10pt;
        }
        table.data-table td { border: 1px solid #000; padding: 8px 12px; font-size: 10pt; }
        
        /* PENOMORAN & RATA TENGAH */
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* TANDA TANGAN */
        .footer-sign { margin-top: 40px; width: 100%; }
        .sign-box { float: right; width: 250px; text-align: center; }
        .space { height: 70px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Pemerintah Provinsi Kalimantan Selatan</h1>
        <h2>SMAN 1 JEJANGKIT</h2>
        <p>Alamat: Jl. SMAN 1 Jejangkit, Kab. Barito Kuala, Kode Pos: 70558</p>
    </div>

    <h3 style="text-align: center; text-decoration: underline; margin-bottom: 20px;">LAPORAN REKAPITULASI NILAI AKHIR SISWA</h3>

    <table class="info-box">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td>: {{ $jadwal->mapel->nama_mapel ?? '-' }}</td>
            <td class="label">Kelas</td>
            <td>: {{ $jadwal->kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Guru Pengajar</td>
            <td>: {{ auth()->user()->name }}</td>
            <td class="label">Semester</td>
            <td>: {{ $jadwal->semester ?? 'Genap' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">No / Rank</th>
                <th width="62%">Nama Lengkap Siswa</th>
                <th width="30%">Nilai Akhir (Rata-rata)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $item)
            <tr>
                <td class="text-center font-bold">{{ $item['ranking'] }}</td>
                <td>{{ $item['nama'] }}</td>
                <td class="text-center font-bold">{{ $item['akhir'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center" style="padding: 20px;">
                    Data nilai tidak ditemukan untuk semester ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Jejangkit, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Guru Mata Pelajaran,</p>
            <div class="space"></div>
            <p><strong>{{ auth()->user()->name }}</strong></p>
            <p>NIP. {{ auth()->user()->guru->nip ?? '..........................' }}</p>
        </div>
    </div>

</body>
</html>