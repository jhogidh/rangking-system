@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Data Kriteria & Pembobotan (ROC)</h4>
                    <p class="card-description">
                        Ubah data <span
                            class="text-success font-weight-bold">Nama Kriteria atau Prioritas</span> di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.kriteria.update', $kriteria->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nama">Nama Kriteria</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Contoh: Nilai Akademik"
                                value="{{ old('nama', $kriteria->nama) }}" required>

                            @error('nama')
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


                        <div class="form-group">
                            <label for="jenis">Jenis Kriteria</label>
                            <select class="form-control @error('jenis') is-invalid @enderror" id="jenis" name="jenis">
                                <option value="benefit" {{ old('jenis', $kriteria->jenis) == 'benefit' ? 'selected' : '' }}>
                                    Benefit
                                </option>
                                <option value="cost" {{ old('jenis', $kriteria->jenis) == 'cost' ? 'selected' : '' }}>
                                    Cost
                                </option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-info mr-2">Update</button>
                        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-warning">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
