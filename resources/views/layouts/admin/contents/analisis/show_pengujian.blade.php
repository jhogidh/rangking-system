@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- 1. FORM FILTER -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Pengujian Metode (Menu 5)</h4>
                    <p class="card-description">
                        Pilih <code>Semester</code> dan <code>Kelas</code> untuk menampilkan data statistik.
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
                    <form class="forms-sample" action="{{ route('admin.analisis.pengujian') }}" method="GET">
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
                                <button type="submit" class="btn btn-success btn-lg btn-block">Tampilkan Statistik</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABEL HASIL STATISTIK (Hanya tampil jika ada filter) -->
        @if ($filters['id_semester'])
            <!-- TABEL 2: STATISTIK AKURASI (SPEARMAN) -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Statistik Akurasi (vs Manual)</h4>
                            <!-- Tombol Hitung Spearman -->
                            <form action="{{ route('admin.analisis.hitung.spearman') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_semester" value="{{ $filters['id_semester'] }}">
                                <input type="hidden" name="id_kelas" value="{{ $filters['id_kelas'] }}">
                                <button type="submit" class="btn btn-secondary btn-sm">Hitung Ulang Akurasi</button>
                            </form>
                        </div>
                        <p class="card-description">
                            Perbandingan akurasi (Korelasi Spearman)
                        </p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>Koefisien Spearman (rho)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($statistik && $statistik->count() > 0)
                                        @foreach ($statistik as $stat)
                                            <tr>
                                                <td>{{ $stat->metode }}</td>
                                                <td>{{ number_format($stat->spearman_rho, 5) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                Tidak ada data. (Apakah data sudah di-hitung di Menu 2 & 3?)
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL 3: STATISTIK KECEPATAN (WAKTU) -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Statistik Kecepatan (ms)</h4>
                        <p class="card-description">
                            Waktu eksekusi per tahap (milidetik)
                        </p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>T1</th>
                                        <th>T2</th>
                                        <th>T3</th>
                                        <th>T4</th>
                                        <th>T5</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($statistik && $statistik->count() > 0)
                                        @foreach ($statistik as $stat)
                                            <tr>
                                                <td>{{ $stat->metode }}</td>
                                                <td>{{ number_format($stat->waktu_tahap_1, 2) }}</td>
                                                <td>{{ number_format($stat->waktu_tahap_2, 2) }}</td>
                                                <td>{{ number_format($stat->waktu_tahap_3, 2) }}</td>
                                                <td>{{ number_format($stat->waktu_tahap_4, 2) }}</td>
                                                <td>{{ number_format($stat->waktu_tahap_5, 2) }}</td>
                                                <td><strong>{{ number_format($stat->waktu_total, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                Tidak ada data. (Apakah data sudah di-hitung di Menu 2 & 3?)
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
