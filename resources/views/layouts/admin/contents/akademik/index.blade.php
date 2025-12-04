@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Mapel Akademik</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>mapel akademik</code> yang terdaftar.
                    </p>
                    <a href="{{ route('proses.akademik.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Mapel Akademik
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
                                    <th>Kode Mapel</th>
                                    <th>Nama Mapel</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akademik as $key => $mapel)
                                    <tr>
                                        <td>{{ $akademik->firstItem() + $key }}</td>
                                        <td>{{ $mapel->kode }}</td>
                                        <td>{{ $mapel->nama }}</td>
                                        <td>
                                            <a href="{{ route('proses.akademik.edit', $mapel->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('proses.akademik.destroy', $mapel->id) }}" method="POST"
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
                                        <td colspan="4" class="text-center">
                                            Tidak ada data mapel akademik.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Link Pagination -->
                    <div class="mt-4">
                        {{ $akademik->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
