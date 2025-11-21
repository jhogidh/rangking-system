@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Kriteria</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>kriteria</code> untuk perankingan.
                    </p>
                    <a href="{{ route('admin.kriteria.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Kriteria Baru
                    </a>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kriteria</th>
                                    <th>Bobot</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kriteria as $key => $k)
                                    <tr>
                                        <td>{{ $kriteria->firstItem() + $key }}</td>
                                        <td>{{ $k->nama_kriteria }}</td>
                                        <td>{{ $k->bobot }}</td>
                                        <td>
                                            <!-- Tombol Aksi -->
                                            <a href="{{ route('admin.kriteria.edit', $k->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('admin.kriteria.destroy', $k->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type...="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            Tidak ada data kriteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Link Pagination -->
                    <div class="mt-4">
                        {{ $kriteria->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
