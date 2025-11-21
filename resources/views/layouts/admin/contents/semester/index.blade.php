@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Semester</h4>
                    <p class="card-description">
                        Berikut adalah daftar <code>semester</code> yang terdaftar.
                    </p>
                    <a href="{{ route('admin.semester.create') }}" class="btn btn-info btn-sm mb-3">
                        Tambah Semester Baru
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
                                    <th>Nama Semester</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semester as $key => $s)
                                    <tr>
                                        <td>{{ $semester->firstItem() + $key }}</td>
                                        <td>{{ $s->nama }}</td>
                                        <!-- Tampilkan nama dari relasi -->
                                        <td>{{ $s->tahunAjaran ? $s->tahunAjaran->tahun_mulai . ' / ' . $s->tahunAjaran->tahun_selesai : 'N/A' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.semester.edit', $s->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ route('admin.semester.destroy', $s->id) }}" method="POST"
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
                                            Tidak ada data semester.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $semester->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
