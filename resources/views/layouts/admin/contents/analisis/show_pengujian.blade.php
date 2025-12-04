@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Pengujian Metode (Menu 5)</h4>
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
                                <button type="submit" class="btn btn-success btn-lg btn-block">Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($filters['id_semester'])
            <!-- TABEL AKURASI -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Statistik Akurasi</h4>
                            <form action="{{ route('admin.analisis.hitung.spearman') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_semester" value="{{ $filters['id_semester'] }}">
                                <input type="hidden" name="id_kelas" value="{{ $filters['id_kelas'] }}">
                                <button type="submit" class="btn btn-secondary btn-sm">Hitung Spearman</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>Spearman (rho)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($statistik && $statistik->count() > 0)
                                        @foreach ($statistik as $stat)
                                            <tr>
                                                <td>{{ $stat->metode }}</td>
                                                <td>{{ number_format($stat->spearman_rho, 5) }}</td>
                                            </tr>
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

            <!-- TABEL KECEPATAN -->
            <div class="col-lg-6 grid-margin stretch-card">
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
                                            <tr>
                                                <td>{{ $stat->metode }}</td>
                                                <td><strong>{{ number_format($stat->waktu_total, 2) }}</strong></td>
                                            </tr>
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
