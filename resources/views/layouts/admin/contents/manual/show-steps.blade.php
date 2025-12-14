@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <!-- KARTU RINGKASAN WAKTU -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card bg-secondary text-white"> <!-- Ganti warna agar beda -->
                <div class="card-body">
                    <h4 class="card-title text-white">Statistik Waktu Eksekusi (Manual)</h4>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6>Tahap 1 (Penjumlahan)</h6>
                            <h3>{{ number_format($timings['tahap_1'], 4) }} ms</h3>
                        </div>
                        <div class="col-md-4">
                            <h6>Tahap 2 (Perangkingan)</h6>
                            <h3>{{ number_format($timings['tahap_2'], 4) }} ms</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Total Waktu</h6>
                            <h2 class="font-weight-bold">{{ number_format($timings['total'], 4) }} ms</h2>
                        </div>
                    </div>
                    <a href="{{ route('admin.manual.index') }}" class="btn btn-light btn-sm mt-3 text-dark">Kembali ke
                        Form</a>
                </div>
            </div>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Perhitungan Manual (SAW)</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Siswa</th>
                                    <th>Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach ($steps['final_scores'] as $altId => $score)
                                    <tr>
                                        <td>{{ $rank++ }}</td>
                                        <td>{{ $siswaMap[$altId] ?? 'N/A' }}</td>
                                        <td>{{ number_format($score, 2) }}</td>
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
