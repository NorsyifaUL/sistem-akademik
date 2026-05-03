@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Tambah Absensi</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('absensi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Siswa</label>
            <select name="siswa_id" class="form-control" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswas as $siswa)
                    <option value="{{ $siswa->id }}">{{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jadwal</label>
            <select name="jadwal_id" class="form-control" required>
                <option value="">-- Pilih Jadwal --</option>
                @foreach($jadwals as $jadwal)
                    <option value="{{ $jadwal->id }}">
                        {{ $jadwal->mapel->nama_mapel }} - {{ $jadwal->guru->nama }} ({{ $jadwal->kelas }})
                        [{{ $jadwal->hari }} {{ $jadwal->jam }}]
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
                <option value="Alfa">Alfa</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan (Opsional)</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection