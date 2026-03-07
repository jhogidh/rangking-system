@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Data Siswa Baru</h4>
                    <p class="card-description">
                        Masukkan data siswa baru di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.siswa.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="kode">Kode Siswa</label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                                name="kode" placeholder="Contoh: S-001" value="{{ old('kode') }}" required>

                            @error('kode')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="nama">Nama Siswa</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Nama Lengkap Siswa" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-info mr-2">Submit</button>

                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-warning">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
