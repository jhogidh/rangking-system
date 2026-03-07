    @extends('layouts.admin.app')

    @section('content')
        <div class="row">
            <!-- RINGKASAN WAKTU WP -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Waktu Respon Metode Weighted Product ({{ $kategoriLabel ?? 'Semua Kriteria' }})</h4>
                            <a href="{{ route('admin.wp.index') }}" class="btn btn-info btn-sm">Kembali</a>
                        </div>
                         <p class="card-description">
                            Waktu ini dihitung setiap tahap hingga total keseluruhan waktu.
                        </p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tahap 1 (Normalisasi Bobot)</th>
                                        <th>Tahap 2 (Vektor S)</th>
                                        <th>Tahap 3 (Vektor V)</th>
                                        <th>Tahap 4 (Ranking)</th>
                                        <th>Total Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ number_format($timings['tahap_1'], 4) }} ms</td>
                                        <td>{{ number_format($timings['tahap_2'], 4) }} ms</td>
                                        <td>{{ number_format($timings['tahap_3'], 4) }} ms</td>
                                        <td>{{ number_format($timings['tahap_4'], 4) }} ms</td>
                                        <td>{{ number_format($timings['total'], 4) }} ms</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LANGKAH 1: BOBOT TERNORMALISASI -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Langkah 1: Normalisasi Bobot</h4>
                        <p class="card-description">
                            Bobot awal dibagi dengan total bobot agar jumlahnya menjadi 1.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        @foreach ($criteria as $c)
                                            <th>{{ $c->nama }}</th>
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        @php $totalNorm = 0; @endphp
                                        @foreach($criteria as $c)
                                            <td>{{ number_format($steps['normalized_weights'][$c->id] ?? 0, 4) }}</td>
                                            @php $totalNorm += ($steps['normalized_weights'][$c->id] ?? 0); @endphp
                                        @endforeach
                                        <td class="font-weight-bold">{{ number_format($totalNorm, 0) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LANGKAH 2: VEKTOR S -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Langkah 2: Menghitung Vektor S</h4>
                        <p class="card-description">
                            Mengalikan nilai kriteria yang sudah dipangkatkan dengan bobot ternormalisasi.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Nilai Vektor S</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($steps['vector_s'] as $altId => $score)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $siswaMap[$altId] ?? 'N/A' }}</td>
                                        <td>{{ number_format($score, 5) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LANGKAH 3 & 4: HASIL AKHIR (VEKTOR V) -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Langkah 3 & 4: Hasil Akhir (Vektor V) & Ranking</h4>
                        <p class="card-description">
                            Vektor V adalah nilai Vektor S dibagi dengan total Vektor S.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ranking</th>
                                        <th>Siswa</th>
                                        <th>Nilai Vektor V (Skor Akhir)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($steps['vector_v'] as $altId => $score)
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
