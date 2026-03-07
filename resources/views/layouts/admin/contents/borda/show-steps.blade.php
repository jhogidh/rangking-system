@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- RINGKASAN WAKTU BORDA -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Waktu Respon Metode Borda ({{ $kategoriLabel ?? 'Semua Kriteria' }})</h4>
                        <a href="{{ route('admin.borda.index') }}" class="btn btn-info btn-sm">Kembali</a>
                    </div>
                    <p class="card-description">
                            Waktu ini dihitung setiap tahap hingga total keseluruhan waktu.
                        </p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tahap 1 (Rank Kriteria)</th>
                                    <th>Tahap 2 (Skor Borda)</th>
                                    <th>Tahap 3 (Bobot ROC)</th>
                                    <th>Tahap 4 (Penjumlahan)</th>
                                    <th>Tahap 5 (Ranking Akhir)</th>
                                    <th>Total Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ number_format($timings['tahap_1'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_2'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_3'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_4'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_5'], 4) }} ms</td>
                                    <td>{{ number_format($timings['total'], 4) }} ms</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LANGKAH 1: RANKING PER KRITERIA -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 1: Ranking per Kriteria</h4>
                                        <p class="card-description">
                            Nilai setiap kriteria yang sudah dihitung di urutkan. 
                        </p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    @foreach($criteria as $c)
                                        <th>{{ $c->nama}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($siswaMap as $altId => $namaSiswa)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $namaSiswa }}</td>
                                    @foreach($criteria as $c)
                                        <td>{{ $steps['ranks_per_criteria'][$c->id][$altId] ?? 'N/A' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LANGKAH 2: SKOR BORDA -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 2: Skor Borda (n - rank)</h4>
                    <p class="card-description">
                            Jumlah dari setiap kelas dikurangi dengan peringkat setiap kriteria yang telah didapat. 
                        </p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    @foreach($criteria as $c)
                                        <th>Skor {{ $c->nama}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($siswaMap as $altId => $namaSiswa)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $namaSiswa }}</td>
                                    @foreach($criteria as $c)
                                        <td>{{ $steps['borda_scores'][$altId][$c->id] ?? 'N/A' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LANGKAH 3: SKOR x BOBOT ROC -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 3: Skor Borda x Bobot ROC</h4>
                    <p class="card-description">
                            Perkalian skor borda dengan pembobotan ROC. 
                        </p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    @foreach($criteria as $c)
                                        <th>{{ $c->nama}} x ( {{ number_format($c->bobot, 4) }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($siswaMap as $altId => $namaSiswa)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $namaSiswa }}</td>
                                    @foreach($criteria as $c)
                                        <td>{{ number_format($steps['weighted_scores'][$altId][$c->id] ?? 0, 4) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LANGKAH 4: PENJUMLAHAN (HASIL AKHIR) -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 4: Penjumlahan Skor Terbobot</h4>
                    Penggabungan menghasilkan nilai akhir setiap alternatif dari berbagai kriteria. 
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    <th>Total Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                <!-- Kita loop siswaMap lagi agar urutannya sama dengan tabel sebelumnya (belum dirangking) -->
                                @foreach($siswaMap as $altId => $namaSiswa)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $namaSiswa }}</td>
                                    <!-- Ambil skor akhir tapi pakai index altId -->
                                    <td>{{ number_format($steps['final_scores'][$altId] ?? 0, 5) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LANGKAH 5: PERANGKINGAN (FINAL) -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 5: Hasil Akhir & Ranking</h4>
                    <p class="card-description">
                            Pengurutan nilai terbesar hingga terkecil. 
                        </p>
                    <div class="table-responsive">
                         <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Siswa</th>
                                    <th>Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop ini otomatis sudah terurut karena $steps['final_scores'] diurutkan di service -->
                                @foreach($steps['final_scores'] as $altId => $score)
                                <tr>
                                    <td class="font-weight-bold">{{ $ranks[$altId] ?? '-' }}</td>
                                    <td>{{ $siswaMap[$altId] ?? 'N/A' }}</td>
                                    <td class="font-weight-bold">{{ number_format($score, 5) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
