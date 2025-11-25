@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Data Kriteria</h4>
                    <p class="card-description">
                        Ubah prioritas atau nama kriteria di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.kriteria.update', $kriteria->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nama_kriteria">Nama Kriteria</label>
                            <input type="text" class="form-control @error('nama_kriteria') is-invalid @enderror"
                                id="nama_kriteria" name="nama_kriteria" placeholder="Contoh: Nilai Akademik"
                                value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required>

                            @error('nama_kriteria')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="prioritas">Prioritas (Ranking)</label>
                            <input type="number" class="form-control @error('prioritas') is-invalid @enderror"
                                id="prioritas" name="prioritas" placeholder="Contoh: 1"
                                value="{{ old('prioritas', $kriteria->prioritas) }}" required min="1">

                            <small class="form-text text-muted">
                                Ubah angka prioritas jika tingkat kepentingan kriteria berubah.
                                (1 = Paling Penting).
                            </small>

                            @error('prioritas')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-light">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
