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

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 1: Normalisasi Bobot (Waktu: {{ number_format($timings['tahap_1'], 4) }}
                        ms)</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @foreach ($criteria as $c)
                                        <th>Bobot {{ $c->nama_kriteria }} (C{{ $c->id }})</th>
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

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-primary">Detail Perhitungan Pangkat (Nilai Awal ^ Bobot Normalisasi)</h4>
                    <p class="card-description">
                        Tabel ini menunjukkan rincian angka sebelum dikalikan menjadi Vektor S.
                        Gunakan tabel ini untuk mencocokkan dengan Excel per kolom kriteria.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="table-primary">
                                    <th rowspan="2" class="text-center align-middle">Nama Siswa</th>
                                    <th colspan="{{ count($criteria) }}" class="text-center">Hasil (Nilai ^ Bobot)</th>
                                    <th rowspan="2" class="text-center align-middle">Hasil Kali (Vektor S)</th>
                                </tr>
                                <tr class="table-primary">
                                    @foreach ($criteria as $c)
                                        <th class="text-center"><small>{{ $c->nama_kriteria }}</small></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop data vector_s_details yang dikirim dari Service --}}
                                @foreach ($steps['vector_s_details'] as $altId => $details)
                                    <tr>
                                        <td>{{ $siswaMap[$altId] ?? 'N/A' }}</td>

                                        {{-- Loop nilai detail per kriteria --}}
                                        @foreach ($criteria as $c)
                                            <td class="text-right">
                                                {{ number_format($details[$c->id] ?? 0, 5) }}
                                            </td>
                                        @endforeach

                                        {{-- Tampilkan Total Vektor S sebagai pembanding --}}
                                        <td class="text-right font-weight-bold">
                                            {{ number_format($steps['vector_s'][$altId] ?? 0, 5) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 2: Rekap Vektor S (Waktu:
                        {{ number_format($timings['tahap_2'], 4) }} ms)</h4>
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

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Langkah 3 & 4: Hasil Akhir (Vektor V) & Ranking (Waktu:
                        {{ number_format($timings['tahap_3'] + $timings['tahap_4'], 4) }} ms)</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Siswa</th>
                                    <th>Hasil Alternatif (Vektor V)</th>
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
