@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Mapel Akademik Baru</h4>
                    <p class="card-description">
                        Masukkan data mapel di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.akademik.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nama">Nama Mapel</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Contoh: Matematika" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kode">Kode Mapel</label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                                name="kode" placeholder="Contoh: MTK-01" value="{{ old('kode') }}" required>
                            @error('kode')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <a href="{{ route('admin.akademik.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
