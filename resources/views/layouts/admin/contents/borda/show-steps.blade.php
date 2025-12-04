@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Perhitungan Borda (Semua Langkah)</h4>
                    <a href="{{ route('admin.borda.index') }}" class="btn btn-secondary btn-sm mb-3">Kembali ke Form</a>
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
                                    <th>Siswa</th>
                                    @foreach ($criteria as $c)
                                        <th>{{ $c->nama_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswaMap as $altId => $namaSiswa)
                                    <tr>
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
                                    <th>Siswa</th>
                                    @foreach ($criteria as $c)
                                        <th>Skor {{ $c->nama_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswaMap as $altId => $namaSiswa)
                                    <tr>
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
