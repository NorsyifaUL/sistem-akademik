<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai - {{ $jadwal->nama_display_kelas }}</title>
    <style>
        /* Pengaturan Dasar */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #333; 
            line-height: 1.4;
        }
        
        /* Header / Kop Surat */
        .header { 
            text-align: center; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header h3 { margin: 5px 0; font-size: 12pt; }
        .header p { margin: 0; font-size: 9pt; font-style: italic; }

        /* Informasi Tabel */
        .info-table { 
            width: 100%; 
            margin-bottom: 20px; 
        }
        .info-table td { 
            padding: 2px 0; 
            vertical-align: top;
        }

        /* Tabel Utama */
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        table.main-table th, table.main-table td { 
            border: 1px solid #000; 
            padding: 8px 5px; 
        }
        table.main-table th { 
            background-color: #f2f2f2; 
            font-size: 9pt; 
            text-transform: uppercase; 
            font-weight: bold;
        }
        
        /* Helpers */
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Tanda Tangan - DIPERBARUI */
        .footer-sign { 
            margin-top: 40px; 
            width: 100%; 
        }
        .sign-box { 
            float: right; 
            width: 250px; 
            text-align: center; 
        }
        /* Menghilangkan margin default paragraf di area ttd */
        .sign-box p { 
            margin: 0; 
            padding: 0;
            line-height: 1.2;
        }
        .space { height: 65px; } /* Jarak untuk tanda tangan basah */
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI NILAI HASIL BELAJAR</h2>
        <h3>SMAN 1 JEJANGKIT</h3>
        <p>Alamat: Jl. Amd Jejangkit Pasar, Barito Kuala, Kalimantan Selatan</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="18%">Mata Pelajaran</td>
            <td width="32%">: <strong>{{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}</strong></td>
            <td width="18%">Semester</td>
            <td width="32%">: {{ ($setting->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $jadwal->nama_display_kelas }}</td>
            <td>Tahun Ajaran</td>
            <td>: {{ $setting->tahun_ajaran ?? '2025/2026' }}</td>
        </tr>
        <tr>
            <td>Guru Mata Pelajaran</td>
            <td>: {{ auth()->user()->name }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Lengkap Siswa</th>
                <th width="12%">Harian</th>
                <th width="12%">UTS</th>
                <th width="12%">UAS</th>
                <th width="15%">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $s)
                @php
                    $nilaiSiswa = $s->nilais->where('jadwal_id', $jadwal->id);
                    
                    $skorUH = $nilaiSiswa->filter(function($item) {
                        $jenisClean = strtolower(str_replace(' ', '', $item->jenis));
                        return preg_match('/uh[1-4]/i', $jenisClean) || $jenisClean == 'harian';
                    })->pluck('nilai')->toArray();

                    $rataUH = count($skorUH) > 0 ? round(array_sum($skorUH) / count($skorUH)) : 0;
                    $uts = $nilaiSiswa->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
                    $uas = $nilaiSiswa->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;
                    $nilaiAkhir = ($rataUH + $uts + $uas) > 0 ? round(($rataUH + $uts + $uas) / 3) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="uppercase">{{ $s->nama }}</td>
                    <td class="text-center">{{ $rataUH ?: '-' }}</td>
                    <td class="text-center">{{ $uts ?: '-' }}</td>
                    <td class="text-center">{{ $uas ?: '-' }}</td>
                    <td class="text-center font-bold">{{ $nilaiAkhir ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Data siswa tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p style="margin-bottom: 5px;">Jejangkit, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p class="font-bold">Guru Mata Pelajaran,</p>
            
            <div class="space"></div>
            
            <p><strong>{{ auth()->user()->name }}</strong></p>
            <p style="border-top: 1px solid #333; display: inline-block; min-width: 190px; margin-top: 3px; padding-top: 2px;">
                NIP. {{ auth()->user()->guru->nip ?? '...........................' }}
            </p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>