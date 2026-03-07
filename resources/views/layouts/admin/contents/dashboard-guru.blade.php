@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                <h6 class="font-weight-normal mb-0">Anda login sebagai <span class="text-success font-weight-bold">Guru / Wali Kelas</span>.</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><i class="icon-upload text-primary"></i> Tugas Utama: Input Data</h4>
                <p class="card-description">
                    Silakan ikuti langkah-langkah berikut untuk memasukkan data nilai siswa.
                </p>
                <div class="list-wrapper pt-2">
                    <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                        <li>
                            <div class="form-check form-check-flat">
                                <label class="form-check-label">
                                    <input class="checkbox" type="checkbox" checked disabled>
                                    Siapkan File Excel "Data Olah"
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check form-check-flat">
                                <label class="form-check-label">
                                    <input class="checkbox" type="checkbox" disabled>
                                    Lakukan <strong>Penempatan Kelas</strong> (Opsional jika pakai import)
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check form-check-flat">
                                <label class="form-check-label">
                                    <input class="checkbox" type="checkbox" disabled>
                                    Lakukan <strong>Import Nilai (Excel)</strong>
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="mt-3">
                    <a href="{{ route('proses.input-nilai.index') }}" class="btn btn-primary btn-block">Mulai Import Data</a>
                </div>
            </div>
        </div>
    </div>

    <!-- FITUR CEK KELENGKAPAN DATA (Requirement 4) -->
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="card-title text-white"><i class="icon-eye text-white"></i> Cek Status Data</h4>
                <p>
                    Sebelum Admin melakukan perankingan, pastikan seluruh data nilai dari Semester 1-6 (Tahun 2022-2025) sudah lengkap terinput.
                </p>
                <div class="d-flex align-items-center justify-content-between mt-4">
                    <div>
                        <h2 class="mb-0 font-weight-bold">Status Data</h2>
                        <p class="mb-0">Monitoring 36 Dataset</p>
                    </div>
                    <a href="{{ route('proses.status-input.index') }}" class="btn btn-light text-primary btn-lg">
                        Cek Kelengkapan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection