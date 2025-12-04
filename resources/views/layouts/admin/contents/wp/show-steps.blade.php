@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Perhitungan WP (Semua Langkah)</h4>
                    <a href="{{ route('admin.wp.index') }}" class="btn btn-secondary btn-sm mb-3">Kembali ke Form</a>
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
                                        <th>{{ $c->nama_kriteria }}</th>
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
                                    <th>Siswa</th>
                                    <th>Nilai Vektor S</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($steps['vector_s'] as $altId => $score)
                                    <tr>
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
