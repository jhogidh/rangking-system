@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ringkasan Akurasi per Kategori</h4>
                    <p class="card-description">Disajikan per kategori tanpa rata-rata gabungan lintas kategori.</p>

                    <div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card bg-success text-white mb-0">
                                <div class="card-body">
                                    <h4 class="card-title text-white">Ringkasan Akurasi WP</h4>
                                    <small class="d-block mb-3">Akurasi All / Top 3, jumlah siswa, sesuai, tidak sesuai per
                                        kategori.</small>

                                    @forelse($accuracySummaryByCategory as $summary)
                                        <div class="{{ !$loop->last ? 'pb-3 mb-3 border-bottom border-light' : '' }}">
                                            <h5 class="text-white mb-2">{{ $summary['label'] }}</h5>
                                            <p class="mb-1">
                                                Akurasi:
                                                <strong>{{ is_null($summary['avg_wp_keseluruhan']) ? '-' : number_format($summary['avg_wp_keseluruhan'], 2) . '%' }}</strong>
                                                /
                                                <strong>{{ is_null($summary['avg_wp_top3']) ? '-' : number_format($summary['avg_wp_top3'], 2) . '%' }}</strong>
                                            </p>
                                            <p class="mb-0">
                                                Siswa: <strong>{{ $summary['jumlah_siswa'] }}</strong> |
                                                Sesuai: <strong>{{ $summary['wp_sesuai'] }}</strong> |
                                                Tidak Sesuai: <strong>{{ $summary['wp_tidak_sesuai'] }}</strong>
                                            </p>
                                        </div>
                                    @empty
                                        <p class="mb-0">Belum ada data akurasi WP.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card bg-warning text-white mb-0">
                                <div class="card-body">
                                    <h4 class="card-title text-white">Ringkasan Akurasi Borda</h4>
                                    <small class="d-block mb-3">Akurasi All / Top 3, jumlah siswa, sesuai, tidak sesuai per
                                        kategori.</small>

                                    @forelse($accuracySummaryByCategory as $summary)
                                        <div class="{{ !$loop->last ? 'pb-3 mb-3 border-bottom border-light' : '' }}">
                                            <h5 class="text-white mb-2">{{ $summary['label'] }}</h5>
                                            <p class="mb-1">
                                                Akurasi:
                                                <strong>{{ is_null($summary['avg_borda_keseluruhan']) ? '-' : number_format($summary['avg_borda_keseluruhan'], 2) . '%' }}</strong>
                                                /
                                                <strong>{{ is_null($summary['avg_borda_top3']) ? '-' : number_format($summary['avg_borda_top3'], 2) . '%' }}</strong>
                                            </p>
                                            <p class="mb-0">
                                                Siswa: <strong>{{ $summary['jumlah_siswa'] }}</strong> |
                                                Sesuai: <strong>{{ $summary['borda_sesuai'] }}</strong> |
                                                Tidak Sesuai: <strong>{{ $summary['borda_tidak_sesuai'] }}</strong>
                                            </p>
                                        </div>
                                    @empty
                                        <p class="mb-0">Belum ada data akurasi Borda.</p>
                                    @endforelse
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

                    <h4 class="card-title">Grafik Akurasi (WP vs Borda)</h4>
                    <h5 id="judulChart">Akademik</h5>

                    <!-- Slide 1 -->
                    <div id="chart1">
                        <canvas id="chartAkademik"></canvas>
                    </div>

                    <!-- Slide 2 -->
                    <div id="chart2" class="d-none">
                        <canvas id="chartNonAkademik"></canvas>
                    </div>

                    <!-- Slide 3 -->
                    <div id="chart3" class="d-none">
                        <canvas id="chartSemua"></canvas>
                    </div>

                    <!-- Tombol -->
                    <div class="mt-3 text-center">
                        <button onclick="prevChart()" class="btn btn-secondary">Prev</button>
                        <button onclick="nextChart()" class="btn btn-primary">Next</button>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Tren Waktu Proses Perhitungan (WP vs Borda)</h4>
                    <p class="card-description">Perbandingan waktu proses perhitungan (ms) di setiap dataset.</p>
                    <canvas id="waktuChart" height="100"></canvas>
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
                                                $rowsKeseluruhan =
                                                    $accuracyTablesByCategory['keseluruhan'][$categoryKey]['rows'] ??
                                                    [];
                                            @endphp
                                            @forelse($rowsKeseluruhan as $row)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $row['dataset_label'] }}</td>
                                                    <td class="text-center">
                                                        {{ $row['wp_sesuai'] }}/{{ $row['wp_tidak_sesuai'] }}</td>
                                                    <td class="text-center">
                                                        {{ is_null($row['akurasi_wp']) ? '-' : number_format($row['akurasi_wp'], 2) . '%' }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $row['borda_sesuai'] }}/{{ $row['borda_tidak_sesuai'] }}</td>
                                                    <td class="text-center">
                                                        {{ is_null($row['akurasi_borda']) ? '-' : number_format($row['akurasi_borda'], 2) . '%' }}
                                                    </td>
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
                                                $rowsTop3 =
                                                    $accuracyTablesByCategory['top_3'][$categoryKey]['rows'] ?? [];
                                            @endphp
                                            @forelse($rowsTop3 as $row)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $row['dataset_label'] }}</td>
                                                    <td class="text-center">
                                                        {{ $row['wp_sesuai'] }}/{{ $row['wp_tidak_sesuai'] }}</td>
                                                    <td class="text-center">
                                                        {{ is_null($row['akurasi_wp']) ? '-' : number_format($row['akurasi_wp'], 2) . '%' }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $row['borda_sesuai'] }}/{{ $row['borda_tidak_sesuai'] }}</td>
                                                    <td class="text-center">
                                                        {{ is_null($row['akurasi_borda']) ? '-' : number_format($row['akurasi_borda'], 2) . '%' }}
                                                    </td>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var canvas = document.getElementById('waktuChart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            var labels = {!! json_encode($chartLabels ?? []) !!};
            var waktuWP = {!! json_encode($chartWaktuWP ?? []) !!};
            var waktuBorda = {!! json_encode($chartWaktuBorda ?? []) !!};

            var ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Waktu WP (ms)',
                            data: waktuWP,
                            borderColor: '#00c851',
                            backgroundColor: 'rgba(0, 200, 81, 0.12)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: false,
                            tension: 0.2
                        },
                        {
                            label: 'Waktu Borda (ms)',
                            data: waktuBorda,
                            borderColor: '#ffbb33',
                            backgroundColor: 'rgba(255, 187, 51, 0.12)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: false,
                            tension: 0.2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Waktu (ms)'
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 90,
                                minRotation: 45
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var value = Number(tooltipItem.yLabel || 0).toFixed(2);
                                return data.datasets[tooltipItem.datasetIndex].label + ': ' + value +
                                    ' ms';
                            }
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                }
            });

            // =====================
            // CHART AKADEMIK
            // =====================
            var ctxAkademik = document.getElementById('chartAkademik');
            if (ctxAkademik) {
                new Chart(ctxAkademik.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labels ?? []) !!},
                        datasets: [{
                                label: 'WP Keseluruhan',
                                data: {!! json_encode($wpAkademikKeseluruhan ?? []) !!}
                            },
                            {
                                label: 'WP Top 3',
                                data: {!! json_encode($wpAkademikTop3 ?? []) !!}
                            },
                            {
                                label: 'Borda Keseluruhan',
                                data: {!! json_encode($bordaAkademikKeseluruhan ?? []) !!}
                            },
                            {
                                label: 'Borda Top 3',
                                data: {!! json_encode($bordaAkademikTop3 ?? []) !!}
                            }
                        ]
                    }
                });
            }

            // =====================
// CHART NON AKADEMIK
// =====================
var ctxNon = document.getElementById('chartNonAkademik');
if (ctxNon) {
    new Chart(ctxNon.getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($labelsNon ?? []) !!},
            datasets: [
                { label: 'WP Keseluruhan', data: {!! json_encode($wpNonKeseluruhan ?? []) !!} },
                { label: 'WP Top 3', data: {!! json_encode($wpNonTop3 ?? []) !!} },
                { label: 'Borda Keseluruhan', data: {!! json_encode($bordaNonKeseluruhan ?? []) !!} },
                { label: 'Borda Top 3', data: {!! json_encode($bordaNonTop3 ?? []) !!} }
            ]
        }
    });
}

// =====================
// CHART SEMUA
// =====================
var ctxSemua = document.getElementById('chartSemua');
if (ctxSemua) {
    new Chart(ctxSemua.getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($labelsSemua ?? []) !!},
            datasets: [
                { label: 'WP Keseluruhan', data: {!! json_encode($wpSemuaKeseluruhan ?? []) !!} },
                { label: 'WP Top 3', data: {!! json_encode($wpSemuaTop3 ?? []) !!} },
                { label: 'Borda Keseluruhan', data: {!! json_encode($bordaSemuaKeseluruhan ?? []) !!} },
                { label: 'Borda Top 3', data: {!! json_encode($bordaSemuaTop3 ?? []) !!} }
            ]
        }
    });
}

        });
        
    </script>
@endsection
