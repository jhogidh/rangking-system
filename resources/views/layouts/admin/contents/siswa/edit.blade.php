@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Data Siswa</h4>
                    <p class="card-description">
                        Ubah data siswa di bawah ini.
                    </p>

                    <!--
                      Form ini menunjuk ke route 'update'
                      dan menggunakan method @method('PUT')
                    -->
                    <form class="forms-sample" action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST">
                        @csrf <!-- Wajib ada untuk keamanan -->
                        @method('PUT') <!-- Wajib ada untuk edit -->

                        <!-- Field 'Kode Siswa' -->
                        <div class="form-group">
                            <label for="kode">Kode Siswa</label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                                name="kode" placeholder="Contoh: S-001" value="{{ old('kode', $siswa->kode) }}" required>
                            <!--
                              old('kode', $siswa->kode)
                              Artinya: Ambil data 'old' (jika validasi gagal),
                              jika tidak ada, ambil data dari database.
                            -->
                            @error('kode')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Field 'Nama Siswa' -->
                        <div class="form-group">
                            <label for="nama">Nama Siswa</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Nama Lengkap Siswa" value="{{ old('nama', $siswa->nama) }}"
                                required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <!-- Tombol Batal -->
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
