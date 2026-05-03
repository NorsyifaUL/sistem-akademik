<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Nilai - Kelas {{ $kelasTerpilih }}</title>
    <style>
        /* Ukuran kertas A4 Landscape biasanya lebih cocok untuk tabel banyak kolom */
        @page { size: landscape; margin: 1cm; }
        
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; padding: 20px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; position: relative; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; }
        
        .info { margin-bottom: 15px; font-weight: bold; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: auto; }
        th, td { border: 1px solid #000; padding: 5px 3px; text-align: center; }
        th { background-color: #f2f2f2 !important; text-transform: uppercase; font-size: 9px; -webkit-print-color-adjust: exact; }
        
        .nama-siswa { text-align: left; padding-left: 8px; font-weight: bold; white-space: nowrap; }
        .rata-rata { background-color: #f9f9f9 !important; font-weight: bold; -webkit-print-color-adjust: exact; }
        .nilai-kurang { color: red; font-weight: bold; }
        
        .footer { margin-top: 30px; float: right; width: 250px; text-align: center; }
        .tanda-tangan { margin-top: 60px; font-weight: bold; text-decoration: underline; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
            /* Memastikan warna background muncul saat diprint */
            th { background-color: #f2f2f2 !important; }
            .rata-rata { background-color: #f9f9f9 !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <span style="float: left; line-height: 35px; color: #64748b; font-weight: bold;">Mode Pratinjau Cetak</span>
        <button onclick="window.print()" style="padding: 8px 16px; background: #1e293b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Sekarang
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>REKAPITULASI NILAI AKHIR SISWA</h1>
        <p style="font-size: 14px; font-weight: bold;">SMAN 1 JEJANGKIT</p>
        <p>Alamat: Jl. Raya Jejangkit, Kec. Jejangkit, Kab. Barito Kuala, Kalimantan Selatan</p>
    </div>

    <div class="info">
        KELAS: {{ $kelasTerpilih }} <br>
        TANGGAL CETAK: {{ date('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="20%">Nama Siswa</th>
                @foreach($mapels as $mapel)
                    <th>{{ $mapel->nama_mapel }}</th>
                @endforeach
                <th width="7%">Rerata</th>
                <th width="5%">Rank</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $index => $siswa)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="nama-siswa">{{ strtoupper($siswa->nama) }}</td>
                
                @foreach($mapels as $mapel)
                    @php 
                        $field = "nilai_mapel_" . $mapel->id;
                        $nilai = $siswa->$field ?? 0;
                    @endphp
                    <td class="{{ $nilai < 75 && $nilai > 0 ? 'nilai-kurang' : '' }}">
                        {{ $nilai > 0 ? number_format($nilai, 1) : '-' }}
                    </td>
                @endforeach

                <td class="rata-rata">{{ number_format($siswa->rata_rata_akhir, 1) }}</td>
                <td style="font-weight: bold;">{{ $index + 1 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($mapels) + 4 }}">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{-- Menggunakan format tanggal Indonesia manual atau via Carbon --}}
        <p>Jejangkit, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p>Mengetahui,</p>
        <p style="margin-bottom: 60px;">Admin Kurikulum,</p>
        <p class="tanda-tangan">( {{ strtoupper(Auth::user()->name) }} )</p>
        <p>NIP. ...........................</p>
    </div>

</body>
</html>