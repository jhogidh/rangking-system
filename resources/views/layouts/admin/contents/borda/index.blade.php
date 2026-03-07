@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Hitung Perankingan Borda</h4>
                    <p class="card-description">
                        Metode Borda menggunakan pendekatan voting dan ranking per kriteria.
                    </p>
                    <!-- Alert Success/Error -->
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form class="forms-sample" action="{{ route('admin.borda.calculate') }}" method="POST">
                        @csrf

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_semester">Pilih Semester</label>
                                    <select class="form-control" id="id_semester" name="id_semester" required>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}">
                                                {{ $semester->nama }}
                                                ({{ $semester->tahun_mulai }}/{{ $semester->tahun_selesai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_kelas">Pilih Kelas</label>
                                    <select class="form-control" id="id_kelas" name="id_kelas">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach ($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}">
                                                {{ $kelas->nama }} {{ $kelas->sub }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="text-left mt-3">

                            <button formaction="{{ route('admin.borda.calculate') }}" type="submit" class="btn btn-info">
                                Borda Semua
                            </button>

                            <button formaction="{{ route('admin.borda.akademik') }}" type="submit"
                                class="btn btn-primary">
                                Borda Akademik
                            </button>

                            <button formaction="{{ route('admin.borda.nonakademik') }}" type="submit"
                                class="btn btn-success">
                                Borda Non Akademik
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
