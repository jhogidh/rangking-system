<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis Perbandingan</title>
    <!-- Tambahkan sedikit style agar rapi -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1,
        h2 {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .success-msg {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <h1>Hasil Analisis Perbandingan</h1>
    <h2>Semester: {{ $semester->nama ?? 'Tidak Ditemukan' }}</h2>

    <!-- Tampilkan pesan sukses jika ada -->
    @if (session('success'))
        <div class="success-msg">
            {{ session('success') }}
        </div>
    @endif

    <!-- 1. Tabel Perbandingan Hasil Ranking -->
    <h2>Perbandingan Hasil Ranking</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th>Rank Manual</th>
                <th>Rank WP</th>
                <th>Rank Borda</th>
            </tr>
        </thead>
        <tbody>
            <!-- $rankings dikelompokkan per nama siswa -->
            @forelse($rankings as $namaSiswa => $hasil)
                <tr>
                    <td>{{ $namaSiswa }}</td>

                    <!-- Cari ranking untuk tiap metode -->
                    <td>{{ $hasil->firstWhere('metode', 'Manual')->ranking ?? 'N/A' }}</td>
                    <td>{{ $hasil->firstWhere('metode', 'WP')->ranking ?? 'N/A' }}</td>
                    <td>{{ $hasil->firstWhere('metode', 'Borda')->ranking ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada data ranking untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. Tabel Statistik Akurasi (Spearman) -->
    <h2>Statistik Akurasi (vs Manual)</h2>
    <table>
        <thead>
            <tr>
                <th>Metode</th>
                <th>Koefisien Korelasi Spearman (rho)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statistik as $stat)
                <tr>
                    <td>{{ $stat->metode }}</td>
                    <td>{{ number_format($stat->spearman_rho, 5) }}</td>
                    <td>
                        @if ($stat->spearman_rho == 1)
                            Sangat Mirip
                        @elseif($stat->spearman_rho > 0.8)
                            Mirip
                        @else
                            Cukup Berbeda
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Tidak ada data statistik akurasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. Tabel Statistik Kecepatan (Waktu) -->
    <h2>Statistik Kecepatan Eksekusi</h2>
    <table>
        <thead>
            <tr>
                <th>Metode</th>
                <th>Tahap 1 (ms)</th>
                <th>Tahap 2 (ms)</th>
                <th>Tahap 3 (ms)</th>
                <th>Tahap 4 (ms)</th>
                <th>Waktu Total (ms)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statistik as $stat)
                <tr>
                    <td>{{ $stat->metode }}</td>
                    <td>{{ number_format($stat->waktu_tahap_1, 4) }}</td>
                    <td>{{ number_format($stat->waktu_tahap_2, 4) }}</td>
                    <td>{{ number_format($stat->waktu_tahap_3, 4) }}</td>
                    <td>{{ number_format($stat->waktu_tahap_4, 4) }}</td>
                    <td><strong>{{ number_format($stat->waktu_total, 4) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data statistik kecepatan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
