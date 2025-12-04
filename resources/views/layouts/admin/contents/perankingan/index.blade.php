@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Mulai Perhitungan SPK</h4>
                    <p class="card-description">
                        Pilih semester dan kelas untuk memulai proses perankingan.
                    </p>

                    <!-- Tampilkan error validasi -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tampilkan pesan error custom -->
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif


                    <form class="forms-sample" action="{{ route('admin.perankingan.hitung') }}" method="POST">
                        @csrf

                        <!-- Dropdown Semester -->
                        <div class="form-group">
                            <label for="id_semester">Pilih Semester</label>
                            <select class="form-control" id="id_semester" name="id_semester" required>
                                <option value="">-- Pilih Semester --</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->nama }} ({{ $semester->tahun_mulai }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Kelas (Opsional) -->
                        <div class="form-group">
                            <label for="id_kelas">Pilih Kelas</label>
                            <select class="form-control" id="id_kelas" name="id_kelas">
                                <!--
                                                  Opsi "Semua Kelas" akan mengirimkan value null,
                                                  yang akan diartikan sebagai "Juara Angkatan/Sekolah"
                                                -->
                                <option value="">-- Semua Kelas (Juara Angkatan) --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama }} {{ $kelas->sub }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Kosongkan untuk menghitung ranking semua kelas di semester
                                ini.</small>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Hitung Perankingan</button>
                        <button type="reset" class="btn btn-light">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
