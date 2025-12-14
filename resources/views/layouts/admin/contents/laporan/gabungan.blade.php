@extends('layouts.admin.app')

@section('content')
    <div class="row">

        <!-- KARTU RINGKASAN (Diperbarui) -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Rata-Rata Akurasi WP</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">{{ number_format($avgWP * 100, 2) }}%</h2>
                            <small class="d-block mt-1">Nilai Rata-rata:
                                <strong>{{ number_format($avgWP, 4) }}</strong></small>
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
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Rata-Rata Akurasi Borda</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">{{ number_format($avgBorda * 100, 2) }}%</h2>
                            <small class="d-block mt-1">Nilai Rata-rata:
                                <strong>{{ number_format($avgBorda, 4) }}</strong></small>
                        </div>
                        <i class="icon-bar-graph icon-lg"></i>
                    </div>
                    <p class="mt-3 mb-0">
                        <i class="icon-layers mr-1"></i> Dari <strong>{{ count($dataset) }}</strong> dataset yang diuji
                    </p>
                </div>
            </div>
        </div>

        <!-- GRAFIK PERBANDINGAN -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Tren Akurasi (WP vs Borda)</h4>
                    <p class="card-description">Perbandingan nilai korelasi Spearman di setiap pengujian.</p>
                    <!-- Canvas untuk Chart.js -->
                    <canvas id="akurasiChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- TABEL REKAPITULASI DENGAN SELISIH -->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tabel Data Lengkap & Analisis Selisih</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">No</th>
                                    <th rowspan="2" class="text-center align-middle">Dataset (Semester - Kelas)</th>
                                    <th colspan="3" class="text-center">Akurasi (Spearman)</th>
                                    <th colspan="2" class="text-center">Waktu (ms)</th>
                                </tr>
                                <tr>
                                    <th class="text-center">WP</th>
                                    <th class="text-center">Borda</th>
                                    <th class="text-center">Selisih (WP - Borda)</th> <!-- Kolom Baru -->
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

                                        <!-- Nilai WP -->
                                        <td
                                            class="text-center font-weight-bold {{ $wpVal > $bordaVal ? 'text-success' : '' }}">
                                            {{ isset($data['WP']['spearman']) ? number_format($wpVal, 4) : '-' }}
                                        </td>

                                        <!-- Nilai Borda -->
                                        <td
                                            class="text-center font-weight-bold {{ $bordaVal > $wpVal ? 'text-success' : '' }}">
                                            {{ isset($data['Borda']['spearman']) ? number_format($bordaVal, 4) : '-' }}
                                        </td>

                                        <!-- Selisih -->
                                        <td class="text-center">
                                            @if ($diff > 0)
                                                <span class="text-primary">+{{ number_format($diff, 4) }} (WP
                                                    Unggul)</span>
                                            @elseif($diff < 0)
                                                <span class="text-info">{{ number_format($diff, 4) }} (Borda Unggul)</span>
                                            @else
                                                <span class="text-muted">0.0000 (Sama)</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            {{ isset($data['WP']['waktu']) ? number_format($data['WP']['waktu'], 2) : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ isset($data['Borda']['waktu']) ? number_format($data['Borda']['waktu'], 2) : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('akurasiChart').getContext('2d');

            // Data dari Controller
            var labels = {!! json_encode($chartLabels) !!};
            var dataWP = {!! json_encode($chartDataWP) !!};
            var dataBorda = {!! json_encode($chartDataBorda) !!};

            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Akurasi WP',
                            data: dataWP,
                            borderColor: '#4B49AC', // Warna Biru
                            backgroundColor: 'rgba(75, 73, 172, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4, // Titik data lebih besar agar mudah dilihat
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.1
                        },
                        {
                            label: 'Akurasi Borda',
                            data: dataBorda,
                            borderColor: '#248AFD', // Warna Biru Muda
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
                        mode: 'index', // Tooltip muncul untuk kedua garis sekaligus saat hover
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 0.5, // Fokus grafik pada range 0.5 - 1.0 agar perbedaan terlihat
                            max: 1.0,
                            title: {
                                display: true,
                                text: 'Koefisien Spearman'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Dataset (Waktu & Kelas)'
                            },
                            ticks: {
                                autoSkip: false, // Tampilkan semua label dataset
                                maxRotation: 90,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(
                                    4); // Tampilkan 4 desimal di tooltip
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
