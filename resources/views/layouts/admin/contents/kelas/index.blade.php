@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Kelas</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>kelas</code> yang terdaftar di sistem.
                    </p>
                    <a href="{{ route('proses.kelas.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Kelas Baru
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
                                    <th>Nama Kelas</th>
                                    <th>Sub Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--
                                                      Loop data $kelas dari controller.
                                                    -->
                                @forelse($kelas as $key => $k)
                                    <tr>
                                        <td>{{ $kelas->firstItem() + $key }}</td>
                                        <td>{{ $k->nama }}</td>
                                        <td>{{ $k->sub ?? '-' }}</td>
                                        <td>
                                            <!-- Tombol Aksi -->
                                            <a href="{{ route('proses.kelas.edit', $k->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('proses.kelas.destroy', $k->id) }}" method="POST"
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
                                            Tidak ada data kelas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Link Pagination -->
                    <div class="mt-4">
                        {{ $kelas->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
