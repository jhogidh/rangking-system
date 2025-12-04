@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Mapel Non-Akademik</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>mapel non-akademik</code> (cth: Ekstrakurikuler).
                    </p>
                    <a href="{{ route('proses.nonakademik.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Non-Akademik
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
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nonakademik as $key => $na)
                                    <tr>
                                        <td>{{ $nonakademik->firstItem() + $key }}</td>
                                        <td>{{ $na->kode }}</td>
                                        <td>{{ $na->nama }}</td>
                                        <td>
                                            <a href="{{ route('proses.nonakademik.edit', $na->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('proses.nonakademik.destroy', $na->id) }}" method="POST"
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
                                            Tidak ada data non-akademik.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Link Pagination -->
                    <div class="mt-4">
                        {{ $nonakademik->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
