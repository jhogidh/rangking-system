@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- 1. FORM FILTER -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Filter Penempatan Kelas</h4>
                    <p class="card-description">
                        Pilih <code>Semester</code> dan <code>Kelas</code> untuk menampilkan data siswa.
                    </p>

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

                    <!-- Form ini menggunakan method GET untuk filter -->
                    <form class="forms-sample" action="{{ route('admin.penempatan.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="id_semester">Pilih Semester</label>
                                    <select class="form-control" id="id_semester" name="id_semester" required>
                                        <option value="">-- Pilih Semester Aktif --</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ request('id_semester') == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->nama }}
                                                ({{ $semester->tahunAjaran->tahun_mulai }}/{{ $semester->tahunAjaran->tahun_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="id_kelas">Pilih Kelas</label>
                                    <select class="form-control" id="id_kelas" name="id_kelas" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelasList as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('id_kelas') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }} {{ $k->sub }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tampilkan hasil HANYA JIKA filter sudah diisi -->
        @if (isset($siswaDiLuarKelas))
            <!-- 2. KOLOM KIRI (SISWA DI LUAR KELAS) -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Siswa (Tersedia)</h4>
                        <p class="card-description">Siswa yang belum ditempatkan di kelas manapun.</p>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Kode</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaDiLuarKelas as $siswa)
                                        <tr>
                                            <td>{{ $siswa->nama }}</td>
                                            <td>{{ $siswa->kode }}</td>
                                            <td>
                                                <!-- Form untuk TAMBAH siswa -->
                                                <form action="{{ route('admin.penempatan.store') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                                                    <input type="hidden" name="id_kelas"
                                                        value="{{ request('id_kelas') }}">
                                                    <input type="hidden" name="id_semester"
                                                        value="{{ request('id_semester') }}">
                                                    <button type="submit" class="btn btn-success btn-sm">Tambahkan</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Semua siswa sudah ditempatkan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. KOLOM KANAN (SISWA DI DALAM KELAS) -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Siswa di Kelas: {{ $kelas->nama ?? '' }}</h4>
                        <p class="card-description">Siswa yang sudah ada di kelas ini.</p>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Kode</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaDiDalamKelas as $data)
                                        <tr>
                                            <td>{{ $data->siswa->nama ?? 'N/A' }}</td>
                                            <td>{{ $data->siswa->kode ?? 'N/A' }}</td>
                                            <td>
                                                <!-- Form untuk HAPUS (mengeluarkan) siswa -->
                                                {{-- <form action="{{ route('admin.penempatan.destroy', $data->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin mengeluarkan siswa ini dari kelas?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Keluarkan</button>
                                                </form> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada siswa di kelas ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
