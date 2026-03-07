@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Status Input Data Nilai (36 Dataset)</h4>
                    <p class="card-description">
                        Monitoring kelengkapan data nilai siswa dari Tahun Ajaran 2022-2024.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">Tahun Ajaran / Semester</th>
                                    <th colspan="{{ $kelasList->count() }}">Kelas</th>
                                </tr>
                                <tr>
                                    @foreach ($kelasList as $kelas)
                                        <th>{{ $kelas->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($semesters as $semester)
                                    <tr>
                                        <td class="text-left font-weight-bold">
                                            {{ $semester->tahun_mulai }}/{{ $semester->tahun_selesai }} -
                                            {{ $semester->nama }}
                                        </td>

                                        @foreach ($kelasList as $kelas)
                                            @php
                                                $isInputted = $statusMatrix[$semester->id][$kelas->id] ?? false;
                                            @endphp
                                            <td>
                                                @if ($isInputted)
                                                    <button type="button" class="btn btn-success btn-rounded btn-icon"
                                                        title="Data Sudah Diinput">
                                                        <i class="ti-check"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-rounded btn-icon"
                                                        title="Data Belum Diinput">
                                                        <i class="ti-close"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h6>Keterangan:</h6>
                        <ul class="list-unstyled">
                            <li><i class="ti-check text-success mr-2"></i> Data Nilai SUDAH diimport</li>
                            <li><i class="ti-close text-danger mr-2"></i> Data Nilai BELUM diimport</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
