@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Data Kriteria Baru</h4>
                    <p class="card-description">
                        Masukkan data kriteria baru di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.kriteria.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nama_kriteria">Nama Kriteria</label>
                            <input type="text" class="form-control @error('nama_kriteria') is-invalid @enderror"
                                id="nama_kriteria" name="nama_kriteria" placeholder="Contoh: Nilai Akademik"
                                value="{{ old('nama_kriteria') }}" required>
                            @error('nama_kriteria')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="prioritas">Prioritas (Ranking)</label>
                            <input type="number" class="form-control @error('prioritas') is-invalid @enderror"
                                id="prioritas" name="prioritas" placeholder="Contoh: 1" value="{{ old('prioritas') }}"
                                required min="1">

                            <small class="form-text text-muted">
                                Tentukan urutan prioritas kriteria.
                                <strong>Angka 1</strong> adalah kriteria paling penting, <strong>2</strong> terpenting
                                kedua, dst.
                                <br><span class="text-danger">*Angka prioritas tidak boleh sama dengan kriteria lain.</span>
                            </small>

                            @error('prioritas')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-light">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
