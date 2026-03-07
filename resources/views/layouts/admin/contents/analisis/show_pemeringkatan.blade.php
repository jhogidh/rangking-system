@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Pemeringkatan</h4>
                    <p class="card-description">
                            Penggabungan hasil peringkat antara manual, Metode WP, dan Metode Borda. 
                        </p>

                    <form class="forms-sample" action="{{ route('admin.analisis.pemeringkatan') }}" method="GET">
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
            @foreach ($categoryLabels as $categoryKey => $categoryLabel)
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Perbandingan Hasil Ranking - {{ $categoryLabel }}</h4>
                            <p class="card-description mb-3">
                                Data diurutkan berdasarkan ranking manual (kategori {{ strtolower($categoryLabel) }}).
                            </p>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Rank Manual</th>
                                            <th>Rank WP</th>
                                            <th>Rank Borda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $rows = $rankingsByCategory[$categoryKey] ?? collect();
                                        @endphp
                                        @if ($rows->count() > 0)
                                            @foreach ($rows as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $row['nama_siswa'] }}</td>
                                                    <td>{{ $row['kelas'] }}</td>
                                                    <td>{{ $row['manual'] ?? 'N/A' }}</td>
                                                    <td>{{ $row['wp'] ?? 'N/A' }}</td>
                                                    <td>{{ $row['borda'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data ranking kategori ini.
                                                    (Sudah dihitung?)</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
