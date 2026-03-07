@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Semester & Tahun Ajaran</h4>
                    <p class="card-description">
                        Berikut adalah daftar <span class="text-success font-weight-bold"> Semester dan Tahun Ajaran </span> yang terdaftar di
                        sistem.
                    </p>
                    <a href="{{ route('proses.semester.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Semester Baru
                    </a>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Semester</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semester as $key => $s)
                                    <tr>
                                        <td>{{ $semester->firstItem() + $key }}</td>
                                        <td>{{ $s->nama }}</td>
                                        <td>{{ $s->tahun_mulai }} / {{ $s->tahun_selesai }}</td>
                                        <td>
                                            <a href="{{ route('proses.semester.edit', $s->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('proses.semester.destroy', $s->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Hapus semester ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $semester->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
