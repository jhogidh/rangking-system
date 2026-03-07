@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <h6 class="font-weight-normal mb-0">Anda login sebagai <span class="text-primary font-weight-bold">
                        Guru BK</span>.</h6>
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

                    <div class="list-wrapper pt-2 overflow-auto h-auto">
                        <ul class="todo-list-custom numbered">
                            <li>
                                <div class="todo-item">
                                  1. Isi <strong> kriteria dan menghitung bobot (ROC)</strong>.
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  2.  Cek kelengkapan yang telah diupload oleh <strong> Admin</strong>.
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  3.  Mengambil <strong> data manual </strong> yang telah diupload oleh Admin sesuai kelas dan tahun.
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  4.  Menghitung <strong> Metode WP</strong>.
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  5.  Menghitung <strong> Metode Borda</strong>. 
                                </div>
                            </li>

                            <li>
                                <div class="todo-item">
                                  6.  Melihat dan menghitung <strong> laporan analisis</strong>.
                                </div>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
