@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- RINGKASAN WAKTU BORDA -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Statistik Waktu Eksekusi (Borda)</h4>
                        <a href="{{ route('admin.borda.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tahap 1 (Rank Kriteria)</th>
                                    <th>Tahap 2 (Skor Borda)</th>
                                    <th>Tahap 3 (Bobot ROC)</th>
                                    <th>Tahap 4 (Penjumlahan)</th>
                                    <th>Tahap 5 (Ranking Akhir)</th>
                                    <th class="bg-success text-white">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ number_format($timings['tahap_1'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_2'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_3'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_4'], 4) }} ms</td>
                                    <td>{{ number_format($timings['tahap_5'], 4) }} ms</td>
                                    <td class="bg-success text-white font-weight-bold">
                                        {{ number_format($timings['total'], 4) }} ms</td>
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
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    @foreach ($criteria as $c)
                                        <th>{{ $c->nama_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($siswaMap as $altId => $namaSiswa)
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>{{ $namaSiswa }}</td>
                                        @foreach ($criteria as $c)
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
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    @foreach ($criteria as $c)
                                        <th>Skor {{ $c->nama_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($siswaMap as $altId => $namaSiswa)
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>{{ $namaSiswa }}</td>
                                        @foreach ($criteria as $c)
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

        <!-- LANGKAH 3 (Optional ditampilkan, bisa skip kalau terlalu panjang) -->

        <!-- HASIL AKHIR -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Akhir & Ranking (Borda)</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Siswa</th>
                                    <th>Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach ($steps['final_scores'] as $altId => $score)
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
