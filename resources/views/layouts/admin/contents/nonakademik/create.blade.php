@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Non-Akademik Baru</h4>
                    <p class="card-description">
                        Masukkan data di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.nonakademik.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nama">Nama Non-Akademik</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Contoh: Pramuka" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kode">Kode</label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                                name="kode" placeholder="Contoh: PRM-01" value="{{ old('kode') }}" required>
                            @error('kode')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <a href="{{ route('admin.nonakademik.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
