@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- RINGKASAN WAKTU WP -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Statistik Waktu Eksekusi (Weighted Product)</h4>
                        <a href="{{ route('admin.wp.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tahap 1 (Normalisasi Bobot)</th>
                                    <th>Tahap 2 (Vektor S)</th>
                                    <th>Tahap 3 (Vektor V)</th>
                                    <th>Tahap 4 (Ranking)</th>
                                    <th class="bg-primary text-white">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ number_format($timings['tahap_1'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_2'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_3'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_4'], 4) }} ms</td>
                                    <td class="bg-primary text-white font-weight-bold">
                                        {{ number_format($timings['total'], 4) }} ms</td>
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
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @foreach ($criteria as $c)
                                        <th>{{ $c->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach ($criteria as $c)
                                        <td>{{ number_format($steps['normalized_weights'][$c->id] ?? 0, 4) }}</td>
                                    @endforeach
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
                    <h4 class="card-title">Langkah 2: Vektor S</h4>
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
                                <?php $i = 1; ?>
                                @foreach ($steps['vector_s'] as $altId => $score)
                                    <tr>
                                        <td><?= $i++ ?></td>
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

        <!-- HASIL AKHIR (VEKTOR V) -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Akhir (Vektor V) & Ranking</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Siswa</th>
                                    <th>Nilai Vektor V</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach ($steps['vector_v'] as $altId => $score)
                                    <tr>
                                        <td>{{ $rank++ }}</td>
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

    </div>
@endsection
