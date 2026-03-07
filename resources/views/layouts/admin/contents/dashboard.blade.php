@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <h6 class="font-weight-normal mb-0">Anda login sebagai <span
                            class="text-success font-weight-bold">Admin</span>.</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="icon-clipboard text-primary"></i> Tugas Utama</h4>
                    <p class="card-description">
                        Silakan ikuti langkah-langkah berikut untuk memenuhi tugas.
                    </p>

                    <div class="list-wrapper pt-2 overflow-hidden h-auto">
                        <ul class="todo-list-custom numbered">
                            <li>
                                <div class="todo-item">
                                   1. Isi <strong> data kelas.</strong>
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                   2. Isi <strong> data semester.</strong>
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  3.  Siapkan file Excel <strong> berupa CSV.</strong>
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  4.  Lakukan <strong> import data </strong> yang berisi rata-rata nilai, nilai sikap, nilai
                                    prestasi, nilai absensi, dan nilai ekstrakurikuler.
                                </div>
                            </li>

                            <p class="card-description">
                               5. Data yang telah terapload akan otomatis masuk dalam data siswa dan kelengkapan data dapat
                                dilihat di cek kelengkapan data.
                            </p>

                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- FITUR CEK KELENGKAPAN DATA (Requirement 4) -->
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="icon-eye text-primary"></i> Cek Status Data</h4>
                    <p>
                        Sebelum Guru BK melakukan perankingan, pastikan seluruh data nilai dari Semester 1-6 (Tahun 2022-2024)
                        sudah lengkap terinput.
                    </p>
                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <div>
                            <h5 class="mb-0 font-weight-bold">Status Data</h5>
                            <p class="mb-0">Monitoring 36 Dataset</p>
                        </div>
                        <a href="{{ route('proses.status-input.index') }}" class="btn btn-info btn-lg">
                            Cek Kelengkapan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
