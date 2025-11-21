@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Tahun Ajaran</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>tahun ajaran</code> yang terdaftar.
                    </p>
                    <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Tahun Ajaran Baru
                    </a>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tahun Mulai</th>
                                    <th>Tahun Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tahunAjaran as $key => $ta)
                                    <tr>
                                        <td>{{ $tahunAjaran->firstItem() + $key }}</td>
                                        <td>{{ $ta->tahun_mulai }}</td>
                                        <td>{{ $ta->tahun_selesai }}</td>
                                        <td>
                                            @if ($ta->status == 'aktif')
                                                <label class="badge badge-success">Aktif</label>
                                            @else
                                                <label class="badge badge-danger">Nonaktif</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.tahun-ajaran.edit', $ta->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Tidak ada data tahun ajaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tahunAjaran->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
