@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- 1. FORM IMPORT -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Import Nilai Kriteria Siswa</h4>
                    <p class="card-description">
                        Fitur ini akan meng-upload 5 nilai kriteria (Nilai, Sikap, Absensi, Ekskul, Prestasi) dari file
                        Excel.<br>
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
                          Form ini HARUS punya enctype="multipart/form-data"
                          dan menunjuk ke route 'store'
                        -->
                    <form class="forms-sample" action="{{ route('admin.input-nilai.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_semester">1. Pilih Semester</label>
                                    <select class="form-control" id="id_semester" name="id_semester" required>
                                        <option value="">-- Pilih Semester Aktif --</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}">
                                                {{ $semester->nama }}
                                                ({{ $semester->tahunAjaran->tahun_mulai }}/{{ $semester->tahunAjaran->tahun_selesai }})
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
                                    <label for="file_import">3. Upload File Excel</label>
                                    <input type="file" name="file_import" class="form-control" id="file_import" required>
                                    <small class="form-text text-muted">Hanya .xlsx, .xls, atau .csv (Gunakan file "Data
                                        Olah")</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Import Data Nilai
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
