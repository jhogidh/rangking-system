@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Kriteria & Pembobotan (ROC)</h4>

                    <div class="d-flex justify-content-between mb-3">
                        <a href="{{ route('admin.kriteria.create') }}" class="btn btn-info btn-sm">
                            <i class="mdi mdi-plus"></i> Tambah Kriteria
                        </a>

                        <form action="{{ route('admin.kriteria.hitung') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="mdi mdi-calculator"></i> Hitung Ulang Bobot (ROC)
                            </button>
                        </form>
                    </div>

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
                                    <th>Prioritas</th>
                                    <th>Nama Kriteria</th>
                                    <th>Bobot (ROC)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kriteria as $k)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $k->prioritas }}</span>
                                        </td>
                                        <td>{{ $k->nama_kriteria }}</td>
                                        <td>
                                            {{ number_format($k->bobot, 4) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.kriteria.edit', $k->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('admin.kriteria.destroy', $k->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Hapus?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2 text-muted text-small">
                        Total Bobot saat ini: <strong>{{ $kriteria->sum('bobot') }}</strong> (Harus mendekati 1)
                    </div>

                    <div class="mt-4">{{ $kriteria->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
