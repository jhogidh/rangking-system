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

                        <!-- Field 'Nama Kriteria' -->
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

                        <!-- Field 'Bobot' -->
                        <div class="form-group">
                            <label for="bobot">Bobot</label>
                            <input type="number" class="form-control @error('bobot') is-invalid @enderror" id="bobot"
                                name="bobot" placeholder="Contoh: 0.25" value="{{ old('bobot') }}" required
                                step="0.01" min="0" max="1">
                            <small class="form-text text-muted">Masukkan angka desimal antara 0 dan 1 (contoh:
                                0.45).</small>
                            @error('bobot')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
