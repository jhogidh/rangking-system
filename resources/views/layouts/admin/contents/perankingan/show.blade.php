@extends('layouts.admin.app')

@section('content')
    <div class="row">

        <!-- JUDUL HALAMAN -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Analisis Perankingan</h4>
                    <p class="card-description">
                        Semester: <strong>{{ $semester->nama }} ({{ $semester->tahun_mulai }})</strong>
                        <br>
                        Kelas: <strong>{{ $kelas->nama ?? 'Semua Kelas (Juara Angkatan/Sekolah)' }}</strong>
                    </p>

                    <!-- Tampilkan pesan sukses dari controller -->
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TABEL 1: PERBANDINGAN HASIL RANKING -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Perbandingan Hasil Ranking</h4>
                    <p class="card-description">
                        Perbandingan hasil ranking akhir dari setiap metode.
                    </p>
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
                                <!-- $rankings adalah data yang sudah dikelompokkan per nama siswa -->
                                @forelse($rankings as $namaSiswa => $hasilSiswa)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $namaSiswa }}</td>

                                        <!-- Ambil ranking dari metode yang sesuai -->
                                        <td>{{ $hasilSiswa->firstWhere('metode', 'Manual')->ranking ?? 'N/A' }}</td>
                                        <td>{{ $hasilSiswa->firstWhere('metode', 'WP')->ranking ?? 'N/A' }}</td>
                                        <td>{{ $hasilSiswa->firstWhere('metode', 'Borda')->ranking ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Tidak ada data ranking untuk ditampilkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL 2: STATISTIK AKURASI (SPEARMAN) -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Statistik Akurasi (vs Manual)</h4>
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
                                @forelse($statistik as $stat)
                                    <tr>
                                        <td>{{ $stat->metode }}</td>
                                        <td>{{ number_format($stat->spearman_rho, 5) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada data.</td>
                                    </tr>
                                @endforelse
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
                        Perbandingan waktu eksekusi per tahap (milidetik)
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th>Tahap 1</th>
                                    <th>Tahap 2</th>
                                    <th>Tahap 3</th>
                                    <th>Tahap 4</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statistik as $stat)
                                    <tr>
                                        <td>{{ $stat->metode }}</td>
                                        <td>{{ number_format($stat->waktu_tahap_1, 4) }}</td>
                                        <td>{{ number_format($stat->waktu_tahap_2, 4) }}</td>
                                        <td>{{ number_format($stat->waktu_tahap_3, 4) }}</td>
                                        <td>{{ number_format($stat->waktu_tahap_4, 4) }}</td>
                                        <td><strong>{{ number_format($stat->waktu_total, 4) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
