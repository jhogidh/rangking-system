@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hasil Pemeringkatan (Menu 5)</h4>
                    <p class="card-description">Pilih <code>Semester</code> dan <code>Kelas</code> untuk menampilkan hasil
                        perbandingan ranking.</p>

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
                                <button type="submit" class="btn btn-success btn-lg btn-block">Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($filters['id_semester'])
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Perbandingan Hasil Ranking</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Rank Manual</th>
                                        <th>Rank WP</th>
                                        <th>Rank Borda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($rankings && $rankings->count() > 0)
                                        @foreach ($rankings as $namaSiswa => $hasilSiswa)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $namaSiswa }}</td>
                                                <td>{{ $hasilSiswa->firstWhere('metode', 'Manual')->ranking ?? 'N/A' }}
                                                </td>
                                                <td>{{ $hasilSiswa->firstWhere('metode', 'WP')->ranking ?? 'N/A' }}</td>
                                                <td>{{ $hasilSiswa->firstWhere('metode', 'Borda')->ranking ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data ranking. (Sudah dihitung?)
                                            </td>
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
