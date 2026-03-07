@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ringkasan Akurasi per Kategori</h4>
                    <p class="card-description">Disajikan per kategori tanpa rata-rata gabungan lintas kategori.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center">WP Keseluruhan</th>
                                    <th class="text-center">Borda Keseluruhan</th>
                                    <th class="text-center">WP Top 3</th>
                                    <th class="text-center">Borda Top 3</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accuracySummaryByCategory as $summary)
                                    <tr>
                                        <td>{{ $summary['label'] }}</td>
                                        <td class="text-center">{{ is_null($summary['avg_wp_keseluruhan']) ? '-' : number_format($summary['avg_wp_keseluruhan'], 2) . '%' }}</td>
                                        <td class="text-center">{{ is_null($summary['avg_borda_keseluruhan']) ? '-' : number_format($summary['avg_borda_keseluruhan'], 2) . '%' }}</td>
                                        <td class="text-center">{{ is_null($summary['avg_wp_top3']) ? '-' : number_format($summary['avg_wp_top3'], 2) . '%' }}</td>
                                        <td class="text-center">{{ is_null($summary['avg_borda_top3']) ? '-' : number_format($summary['avg_borda_top3'], 2) . '%' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data akurasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ringkasan Kecepatan</h4>
                    <p class="card-description">Rata-rata waktu proses perhitungan metode.</p>

                    <div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card bg-success text-white mb-0">
                                <div class="card-body">
                                    <h4 class="card-title text-white">Rata-Rata Waktu WP</h4>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h2 class="mb-0">{{ number_format($avgWaktuWP ?? 0, 2) }} ms</h2>
                                            <small class="d-block mt-1">Rata-rata waktu eksekusi</small>
                                        </div>
                                        <i class="icon-timer icon-lg"></i>
                                    </div>
                                    <p class="mt-3 mb-0">
                                        <i class="icon-layers mr-1"></i> Dari <strong>{{ count($dataset) }}</strong>
                                        dataset
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card bg-warning text-white mb-0">
                                <div class="card-body">
                                    <h4 class="card-title text-white">Rata-Rata Waktu Borda</h4>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h2 class="mb-0">{{ number_format($avgWaktuBorda ?? 0, 2) }} ms</h2>
                                            <small class="d-block mt-1">Rata-rata waktu eksekusi</small>
                                        </div>
                                        <i class="icon-timer icon-lg"></i>
                                    </div>
                                    <p class="mt-3 mb-0">
                                        <i class="icon-layers mr-1"></i> Dari <strong>{{ count($dataset) }}</strong>
                                        dataset
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tabel Data Waktu</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Dataset (Semester - Kelas)</th>
                                    <th class="text-center">Waktu WP (ms)</th>
                                    <th class="text-center">Waktu Borda (ms)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($dataset as $data)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $data['semester'] }} - {{ $data['kelas'] }}</td>
                                        <td class="text-center">
                                            {{ isset($data['WP']['waktu']) ? number_format($data['WP']['waktu'], 2) : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ isset($data['Borda']['waktu']) ? number_format($data['Borda']['waktu'], 2) : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($accuracySummaryByCategory as $categoryKey => $summary)
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Rekap Akurasi - {{ $summary['label'] }}</h4>
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mb-3">Keseluruhan Kelas</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th>Dataset</th>
                                                <th class="text-center">WP (S/T)</th>
                                                <th class="text-center">Akurasi WP</th>
                                                <th class="text-center">Borda (S/T)</th>
                                                <th class="text-center">Akurasi Borda</th>
                                                <th class="text-center">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $rowsKeseluruhan = $accuracyTablesByCategory['keseluruhan'][$categoryKey]['rows'] ?? [];
                                            @endphp
                                            @forelse($rowsKeseluruhan as $row)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $row['dataset_label'] }}</td>
                                                    <td class="text-center">{{ $row['wp_sesuai'] }}/{{ $row['wp_tidak_sesuai'] }}</td>
                                                    <td class="text-center">{{ is_null($row['akurasi_wp']) ? '-' : number_format($row['akurasi_wp'], 2) . '%' }}</td>
                                                    <td class="text-center">{{ $row['borda_sesuai'] }}/{{ $row['borda_tidak_sesuai'] }}</td>
                                                    <td class="text-center">{{ is_null($row['akurasi_borda']) ? '-' : number_format($row['akurasi_borda'], 2) . '%' }}</td>
                                                    <td class="text-center">{{ $row['jumlah_manual'] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Tidak ada data.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="mb-3">Top 3</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th>Dataset</th>
                                                <th class="text-center">WP (S/T)</th>
                                                <th class="text-center">Akurasi WP</th>
                                                <th class="text-center">Borda (S/T)</th>
                                                <th class="text-center">Akurasi Borda</th>
                                                <th class="text-center">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $rowsTop3 = $accuracyTablesByCategory['top_3'][$categoryKey]['rows'] ?? [];
                                            @endphp
                                            @forelse($rowsTop3 as $row)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $row['dataset_label'] }}</td>
                                                    <td class="text-center">{{ $row['wp_sesuai'] }}/{{ $row['wp_tidak_sesuai'] }}</td>
                                                    <td class="text-center">{{ is_null($row['akurasi_wp']) ? '-' : number_format($row['akurasi_wp'], 2) . '%' }}</td>
                                                    <td class="text-center">{{ $row['borda_sesuai'] }}/{{ $row['borda_tidak_sesuai'] }}</td>
                                                    <td class="text-center">{{ is_null($row['akurasi_borda']) ? '-' : number_format($row['akurasi_borda'], 2) . '%' }}</td>
                                                    <td class="text-center">{{ $row['jumlah_manual'] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Tidak ada data.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
