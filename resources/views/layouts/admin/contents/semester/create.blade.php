@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Semester Baru</h4>
                    <form class="forms-sample" action="{{ route('admin.semester.store') }}" method="POST">
                        @csrf

                        <!-- Nama Semester -->
                        <div class="form-group">
                            <label for="nama">Nama Semester</label>
                            <select class="form-control" name="nama" id="nama">
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>

                        <!-- Tahun Mulai -->
                        <div class="form-group">
                            <label for="tahun_mulai">Tahun Mulai</label>
                            <input type="number" class="form-control" name="tahun_mulai" placeholder="2024" required>
                        </div>

                        <!-- Tahun Selesai -->
                        <div class="form-group">
                            <label for="tahun_selesai">Tahun Selesai</label>
                            <input type="number" class="form-control" name="tahun_selesai" placeholder="2025" required>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="aktif">Aktif</option>
                            </select>
                            <small class="text-muted">Jika dipilih Aktif, semester lain otomatis jadi Nonaktif.</small>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                        <a href="{{ route('admin.semester.index') }}" class="btn btn-light">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
