@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- 1. FORM IMPORT -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Import Nilai Kriteria Siswa</h4>
                    <p class="card-description">
                        Gunakan fitur ini untuk meng-upload nilai 5 kriteria (Nilai, Sikap, Absensi, Ekskul, Prestasi)
                        langsung dari file Excel (Data Olah).<br>
                        <strong>PENTING:</strong> Jika nama siswa di Excel belum ada di database, sistem akan **otomatis
                        membuat siswa baru** dan **menempatkannya** di kelas ini.
                    </p>

                    <!-- Tampilkan pesan sukses/error -->
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!--
                          PERBAIKAN RUTE:
                          Ganti 'admin.input-nilai.store' menjadi 'proses.input-nilai.store'
                        -->
                    <form class="forms-sample" action="{{ route('proses.input-nilai.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_semester">1. Pilih Semester</label>
                                    <select class="form-control" id="id_semester" name="id_semester" required>
                                        <option value="">-- Pilih Semester Aktif --</option>
                                        @foreach ($semesters as $semester)
                                            <!-- PERBAIKAN TAHUN AJARAN: Ambil langsung dari semester -->
                                            <option value="{{ $semester->id }}">
                                                {{ $semester->nama }}
                                                ({{ $semester->tahun_mulai }}/{{ $semester->tahun_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_kelas">2. Pilih Kelas</label>
                                    <select class="form-control" id="id_kelas" name="id_kelas" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelasList as $k)
                                            <option value="{{ $k->id }}">
                                                {{ $k->nama }} {{ $k->sub }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="file_import">3. Upload File Excel/CSV</label>
                                    <input type="file" name="file_import" class="form-control" id="file_import" required>
                                    <small class="form-text text-muted">Gunakan file "Data Olah.csv"</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="ti-upload"></i> Import Data Nilai
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
