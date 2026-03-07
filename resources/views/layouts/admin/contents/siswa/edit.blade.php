@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
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
                    <form class="forms-sample" action="{{ route('proses.siswa.update', $siswa->id) }}" method="POST">
                        @csrf <!-- Wajib ada untuk keamanan -->
                        @method('PUT') <!-- Wajib ada untuk edit -->

                        <!-- Field 'Nisn Siswa' -->
                        <div class="form-group">
                            <label for="nisn">Nisn Siswa</label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                                name="nisn" placeholder="Contoh: S-001" value="{{ old('nisn', $siswa->nisn) }}" required>
                            <!--
                                                  old('kode', $siswa->kode)
                                                  Artinya: Ambil data 'old' (jika validasi gagal),
                                                  jika tidak ada, ambil data dari database.
                                                -->
                            @error('nisn')
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

                        <!-- Field 'Tahun Masuk' -->
                        <div class="form-group">
                            <label for="tahun_masuk">Tahun Masuk</label>
                            <input type="number" class="form-control @error('tahun_masuk') is-invalid @enderror"
                                id="tahun_masuk" name="tahun_masuk" placeholder="Contoh: 2024"
                                value="{{ old('tahun_masuk', $siswa->tahun_masuk) }}" required>
                            @error('tahun_masuk')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <!-- Tombol Batal -->
                        <a href="{{ route('proses.siswa.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
