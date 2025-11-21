@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- 1. FORM FILTER -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Pemeringkatan (Menu 4)</h4>
                    <p class="card-description">
                        Pilih <code>Semester</code> dan <code>Kelas</code> untuk menampilkan hasil perbandingan ranking.
                    </p>

                    <!-- Pesan Sukses/Error -->
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

                    <!-- Form ini menggunakan method GET untuk filter -->
                    <form class="forms-sample" action="{{ route('admin.analisis.pemeringkatan') }}" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="id_semester">Pilih Semester</label>
                                    <select class="form-control" id="id_semester" name="id_semester" required>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach ($dropdowns['semesters'] as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ $filters['id_semester'] == $semester->id ? 'selected' : '' }}>
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
                                    <select class="form-control" id="id_kelas" name="id_kelas">
                                        <option value="">-- Semua Kelas (Juara Angkatan) --</option>
                                        @foreach ($dropdowns['kelasList'] as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $filters['id_kelas'] == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }} {{ $k->sub }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-success btn-lg btn-block">Tampilkan Hasil</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABEL HASIL RANKING (Hanya tampil jika ada filter) -->
        @if ($filters['id_semester'])
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Perbandingan Hasil Ranking</h4>
                            <!-- Tombol Hitung Manual -->
                            <form action="{{ route('admin.analisis.hitung.manual') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_semester" value="{{ $filters['id_semester'] }}">
                                <input type="hidden" name="id_kelas" value="{{ $filters['id_kelas'] }}">
                                <button type="submit" class="btn btn-secondary btn-sm">Hitung Ulang Manual (SAW)</button>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Rank Manual</th>
                                        <th>Rank WP</th>
                                        <th>Rank Borda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($rankings && $rankings->count() > 0)
                                        @foreach ($rankings as $namaSiswa => $hasilSiswa)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $namaSiswa }}</td>
                                                <td>{{ $hasilSiswa->firstWhere('metode', 'Manual')->ranking ?? 'N/A' }}
                                                </td>
                                                <td>{{ $hasilSiswa->firstWhere('metode', 'WP')->ranking ?? 'N/A' }}</td>
                                                <td>{{ $hasilSiswa->firstWhere('metode', 'Borda')->ranking ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Tidak ada data ranking untuk ditampilkan. (Apakah data sudah di-import dan
                                                di-hitung di Menu 2 & 3?)
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
