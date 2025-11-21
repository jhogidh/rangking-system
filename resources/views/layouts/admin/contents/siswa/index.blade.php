@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Siswa</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>siswa</code> yang terdaftar di sistem.
                    </p>
                    <a href="{{ route('admin.siswa.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Siswa Baru
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
                                    <th>Kode Siswa</th>
                                    <th>Nama Siswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--
                                              Loop data $siswa dari controller.
                                            -->
                                @forelse($siswa as $key => $s)
                                    <tr>
                                        <td>{{ $siswa->firstItem() + $key }}</td>
                                        <td>{{ $s->kode }}</td>
                                        <td>{{ $s->nama }}</td>
                                        <td>
                                            <!-- Tombol Aksi -->
                                            <a href="{{ route('admin.siswa.edit', $s->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('admin.siswa.destroy', $s->id) }}" method="POST"
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
                                            Tidak ada data siswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $siswa->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
