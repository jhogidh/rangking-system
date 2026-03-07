@extends('layouts.admin.app')

@section('content')
    <style>
        /* Biar konten slide tidak ketutup tombol prev/next */
        #ringkasanCarousel .carousel-inner {
            padding-left: 48px;
            /* ruang buat tombol kiri */
            padding-right: 48px;
            /* ruang buat tombol kanan */
        }

        /* (opsional) rapikan posisi tombol biar di luar area konten */
        #ringkasanCarousel .carousel-control-prev {
            left: 0;
            width: 48px;
        }

        #ringkasanCarousel .carousel-control-next {
            right: 0;
            width: 48px;
        }

        #ringkasanCarousel .carousel-indicators li {
            background-color: rgba(0, 0, 0, .35);
            width: 10px;
            height: 10px;
            border-radius: 999px;
        }

        #ringkasanCarousel .carousel-indicators .active {
            background-color: rgba(0, 0, 0, .85);
        }
    </style>
    <div class="row">

        {{-- =======================
        SLIDER RINGKASAN (2 SLIDE)
        Slide 1: Akurasi (2 kartu)
        Slide 2: Waktu (2 kartu)
    ======================== --}}
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ringkasan Pengujian</h4>
                    <p class="card-description">Melihat ringkasan waktu.</p>

                    <div id="ringkasanCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                        {{--<ol class="carousel-indicators">
                            <li data-target="#ringkasanCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#ringkasanCarousel" data-slide-to="1"></li> 
                        </ol> --}}


                        <div class="carousel-inner">

                            {{-- Slide 1: Akurasi --}}
                            {{--  <div class="carousel-item active">
                            <div class="row">

                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card bg-primary text-white mb-0">
                                        <div class="card-body">
                                            <h4 class="card-title text-white">Rata-Rata Akurasi WP</h4>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h2 class="mb-0">{{ number_format(($avgWP ?? 0) * 100, 2) }}%</h2>
                                                    <small class="d-block mt-1">Nilai Rata-rata:
                                                        <strong>{{ number_format($avgWP ?? 0, 4) }}</strong>
                                                    </small>
                                                </div>
                                                <i class="icon-bar-graph icon-lg"></i>
                                            </div>
                                            <p class="mt-3 mb-0">
                                                <i class="icon-layers mr-1"></i> Dari <strong>{{ count($dataset) }}</strong> dataset yang diuji
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card bg-info text-white mb-0">
                                        <div class="card-body">
                                            <h4 class="card-title text-white">Rata-Rata Akurasi Borda</h4>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h2 class="mb-0">{{ number_format(($avgBorda ?? 0) * 100, 2) }}%</h2>
                                                    <small class="d-block mt-1">Nilai Rata-rata:
                                                        <strong>{{ number_format($avgBorda ?? 0, 4) }}</strong>
                                                    </small>
                                                </div>
                                                <i class="icon-bar-graph icon-lg"></i>
                                            </div>
                                            <p class="mt-3 mb-0">
                                                <i class="icon-layers mr-1"></i> Dari <strong>{{ count($dataset) }}</strong> dataset yang diuji
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div> --}}

                            {{-- Slide 2: Waktu --}}
                            <div class="carousel-item active">
                                <div class="row">

                                    <div class="col-md-6 grid-margin stretch-card">
                                        <div class="card bg-success text-white mb-0">
                                            <div class="card-body">
                                                <h4 class="card-title text-white">Rata-Rata Waktu WP</h4>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h2 class="mb-0">{{ number_format($avgWaktuWP ?? 0, 2) }} ms</h2>
                                                        <small class="d-block mt-1">Rata-rata:
                                                            <strong>{{ number_format($avgWaktuWP ?? 0, 2) }}</strong> ms
                                                        </small>
                                                    </div>
                                                    <i class="icon-timer icon-lg"></i>
                                                </div>
                                                <p class="mt-3 mb-0">
                                                    <i class="icon-layers mr-1"></i> Dari
                                                    <strong>{{ count($dataset) }}</strong> dataset yang diuji
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
                                                        <h2 class="mb-0">{{ number_format($avgWaktuBorda ?? 0, 2) }} ms
                                                        </h2>
                                                        <small class="d-block mt-1">Rata-rata:
                                                            <strong>{{ number_format($avgWaktuBorda ?? 0, 2) }}</strong> ms
                                                        </small>
                                                    </div>
                                                    <i class="icon-timer icon-lg"></i>
                                                </div>
                                                <p class="mt-3 mb-0">
                                                    <i class="icon-layers mr-1"></i> Dari
                                                    <strong>{{ count($dataset) }}</strong> dataset yang diuji
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                         {{--<a class="carousel-control-prev" href="#ringkasanCarousel" role="button" data-slide="prev">
                            <span class="btn btn-dark btn-sm" aria-hidden="true">‹</span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#ringkasanCarousel" role="button" data-slide="next">
                            <span class="btn btn-dark btn-sm" aria-hidden="true">›</span>
                            <span class="sr-only">Next</span>
                        </a>--}}

                    </div>
                </div>
            </div>
        </div>

        {{-- =======================
        GRAFIK AKURASI
    ======================== --}}
        {{-- ======================= <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Grafik Tren Akurasi (WP vs Borda)</h4>
                <p class="card-description">Perbandingan nilai korelasi Spearman di setiap pengujian.</p>
                <canvas id="akurasiChart" height="100"></canvas>
            </div>
        </div>
    </div>======================== --}}

        {{-- =======================
        GRAFIK WAKTU
    ======================== --}}
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Tren Waktu Proses Perhitungan (WP vs Borda)</h4>
                    <p class="card-description">Perbandingan waktu proses perhitungan (ms) di setiap pengujian.</p>
                    <canvas id="waktuChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- =======================
        TABEL REKAPITULASI
    ======================== --}}
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tabel Data Lengkap </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">No</th>
                                    <th rowspan="2" class="text-center align-middle">Dataset (Semester - Kelas)</th>
                                    {{-- <th colspan="3" class="text-center">Akurasi (Spearman)</th> --}}
                                    <th colspan="2" class="text-center">Waktu (ms)</th>
                                </tr>
                                <tr>
                                    {{-- <th class="text-center">WP</th>
                            <th class="text-center">Borda</th>
                            <th class="text-center">Selisih (WP - Borda)</th> --}}
                                    <th class="text-center">WP</th>
                                    <th class="text-center">Borda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($dataset as $key => $data)
                                    @php
                                        $wpVal = $data['WP']['spearman'] ?? 0;
                                        $bordaVal = $data['Borda']['spearman'] ?? 0;
                                        $diff = $wpVal - $bordaVal;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $data['semester'] }} - {{ $data['kelas'] }}</td>

                                      {{--  <td
                                            class="text-center font-weight-bold {{ $wpVal > $bordaVal ? 'text-success' : '' }}">
                                            {{ isset($data['WP']['spearman']) ? number_format($wpVal, 4) : '-' }}
                                        </td>

                                        <td
                                            class="text-center font-weight-bold {{ $bordaVal > $wpVal ? 'text-success' : '' }}">
                                            {{ isset($data['Borda']['spearman']) ? number_format($bordaVal, 4) : '-' }}
                                        </td>

                                        <td class="text-center">
                                            @if ($diff > 0)
                                                <span class="text-primary">+{{ number_format($diff, 4) }} (WP
                                                    Unggul)</span>
                                            @elseif($diff < 0)
                                                <span class="text-info">{{ number_format($diff, 4) }} (Borda Unggul)</span>
                                            @else
                                                <span class="text-muted">0.0000 (Sama)</span>
                                            @endif
                                        </td>--}}

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

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Rekap Akurasi Keseluruhan Kelas per Kategori</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Dataset</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Spearman WP</th>
                                    <th class="text-center">Spearman Borda</th>
                                    <th class="text-center">Jumlah Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noAkurasiAll = 1; @endphp
                                @forelse($accuracyByScope['keseluruhan'] ?? [] as $row)
                                    <tr>
                                        <td class="text-center">{{ $noAkurasiAll++ }}</td>
                                        <td>{{ $row['dataset_label'] }}</td>
                                        <td>{{ $row['kategori_label'] }}</td>
                                        <td class="text-center">
                                            {{ is_null($row['spearman_wp']) ? '-' : number_format($row['spearman_wp'], 5) }}
                                        </td>
                                        <td class="text-center">
                                            {{ is_null($row['spearman_borda']) ? '-' : number_format($row['spearman_borda'], 5) }}
                                        </td>
                                        <td class="text-center">{{ $row['jumlah_manual'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data akurasi.</td>
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
                    <h4 class="card-title">Rekap Akurasi Top 3 per Kategori</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Dataset</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Spearman WP</th>
                                    <th class="text-center">Spearman Borda</th>
                                    <th class="text-center">Jumlah Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noAkurasiTop = 1; @endphp
                                @forelse($accuracyByScope['top_3'] ?? [] as $row)
                                    <tr>
                                        <td class="text-center">{{ $noAkurasiTop++ }}</td>
                                        <td>{{ $row['dataset_label'] }}</td>
                                        <td>{{ $row['kategori_label'] }}</td>
                                        <td class="text-center">
                                            {{ is_null($row['spearman_wp']) ? '-' : number_format($row['spearman_wp'], 5) }}
                                        </td>
                                        <td class="text-center">
                                            {{ is_null($row['spearman_borda']) ? '-' : number_format($row['spearman_borda'], 5) }}
                                        </td>
                                        <td class="text-center">{{ $row['jumlah_manual'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data akurasi top 3.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const labels = {!! json_encode($chartLabels) !!};

            /*// ===== Chart Akurasi =====
            const dataWP = {!! json_encode($chartDataWP) !!};
            const dataBorda = {!! json_encode($chartDataBorda) !!};

            const ctxAkurasi = document.getElementById('akurasiChart').getContext('2d');
            new Chart(ctxAkurasi, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Akurasi WP',
                            data: dataWP,
                            borderColor: '#4B49AC',
                            backgroundColor: 'rgba(75, 73, 172, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.1
                        },
                        {
                            label: 'Akurasi Borda',
                            data: dataBorda,
                            borderColor: '#248AFD',
                            backgroundColor: 'rgba(36, 138, 253, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 0.5,
                            max: 1.0,
                            title: {
                                display: true,
                                text: 'Koefisien Spearman'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Dataset (Semester & Kelas)'
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(4);
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });*/

            // ===== Chart Waktu =====
            const waktuWP = {!! json_encode($chartWaktuWP) !!};
            const waktuBorda = {!! json_encode($chartWaktuBorda) !!};

            const ctxWaktu = document.getElementById('waktuChart').getContext('2d');
            new Chart(ctxWaktu, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Waktu WP (ms)',
                            data: waktuWP,
                            borderColor: '#00c851',
                            backgroundColor: 'rgba(0, 200, 81, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.1
                        },
                        {
                            label: 'Waktu Borda (ms)',
                            data: waktuBorda,
                            borderColor: '#ffbb33',
                            backgroundColor: 'rgba(255, 187, 51, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
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
                            title: {
                                display: true,
                                text: 'Dataset (Semester & Kelas)'
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const v = (context.parsed.y ?? 0);
                                    return context.dataset.label + ': ' + Number(v).toFixed(2) + ' ms';
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        });
    </script>
@endsection
