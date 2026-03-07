@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Pengujian Metode</h4>
                    <p class="card-description">
                           Pengujian waktu proses perhitungan.
                        </p>
                    <!-- Form Filter (sama seperti Pemeringkatan, copy paste jika perlu) -->
                    <form class="forms-sample" action="{{ route('admin.analisis.pengujian') }}" method="GET">
                        <!-- ... (Kode form sama dengan show_pemeringkatan.blade.php) ... -->
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="id_semester">Pilih Semester</label>
                                    <select class="form-control" id="id_semester" name="id_semester" required>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach ($dropdowns['semesters'] as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ $filters['id_semester'] == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->nama }}
                                                ({{ $semester->tahun_mulai }}/{{ $semester->tahun_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="id_kelas">Pilih Kelas</label>
                                    <select class="form-control" id="id_kelas" name="id_kelas">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach ($dropdowns['kelasList'] as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $filters['id_kelas'] == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }} {{ $k->sub }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-info btn-sm py-3 btn-block">Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($filters['id_semester'])
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Akurasi Keseluruhan Kelas</h4>
                        <p class="card-description">Label `Sesuai` jika ranking metode sama persis dengan manual.</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>WP (S/T)</th>
                                        <th>Akurasi WP</th>
                                        <th>Label WP</th>
                                        <th>Borda (S/T)</th>
                                        <th>Akurasi Borda</th>
                                        <th>Label Borda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $overallRows = $accuracyByScope['keseluruhan'] ?? []; @endphp
                                    @if (count($overallRows) > 0)
                                        @foreach ($overallRows as $row)
                                            <tr>
                                                <td>{{ $row['kategori_label'] }}</td>
                                                <td>{{ $row['wp_sesuai'] }}/{{ $row['wp_tidak_sesuai'] }}</td>
                                                <td>{{ is_null($row['akurasi_wp']) ? '-' : number_format($row['akurasi_wp'], 2) . '%' }}
                                                </td>
                                                <td>{{ $row['label_wp'] }}</td>
                                                <td>{{ $row['borda_sesuai'] }}/{{ $row['borda_tidak_sesuai'] }}</td>
                                                <td>{{ is_null($row['akurasi_borda']) ? '-' : number_format($row['akurasi_borda'], 2) . '%' }}
                                                </td>
                                                <td>{{ $row['label_borda'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data akurasi.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Akurasi Top 3 (Berdasarkan Manual)</h4>
                        <p class="card-description">S = Sesuai, T = Tidak Sesuai.</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>WP (S/T)</th>
                                        <th>Akurasi WP</th>
                                        <th>Label WP</th>
                                        <th>Borda (S/T)</th>
                                        <th>Akurasi Borda</th>
                                        <th>Label Borda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $topRows = $accuracyByScope['top_3'] ?? []; @endphp
                                    @if (count($topRows) > 0)
                                        @foreach ($topRows as $row)
                                            <tr>
                                                <td>{{ $row['kategori_label'] }}</td>
                                                <td>{{ $row['wp_sesuai'] }}/{{ $row['wp_tidak_sesuai'] }}</td>
                                                <td>{{ is_null($row['akurasi_wp']) ? '-' : number_format($row['akurasi_wp'], 2) . '%' }}
                                                </td>
                                                <td>{{ $row['label_wp'] }}</td>
                                                <td>{{ $row['borda_sesuai'] }}/{{ $row['borda_tidak_sesuai'] }}</td>
                                                <td>{{ is_null($row['akurasi_borda']) ? '-' : number_format($row['akurasi_borda'], 2) . '%' }}
                                                </td>
                                                <td>{{ $row['label_borda'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data akurasi top 3.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL KECEPATAN -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Statistik Kecepatan (ms)</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>Total Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($statistik && $statistik->count() > 0)
                                        @foreach ($statistik as $stat)
                                        @if($stat->metode != "Manual")
                                            <tr>
                                                <td>{{ $stat->metode }}</td>
                                                <td><strong>{{ number_format($stat->waktu_total, 2) }}</strong></td>
                                            </tr>
                                        @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">Tidak ada data.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
