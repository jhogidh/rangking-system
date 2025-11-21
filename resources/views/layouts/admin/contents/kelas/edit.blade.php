@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Data Kelas</h4>
                    <p class="card-description">
                        Ubah data kelas di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.kelas.update', $kelas->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Field 'Nama Kelas' -->
                        <div class="form-group">
                            <label for="nama">Nama Kelas</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Contoh: 10A" value="{{ old('nama', $kelas->nama) }}" required>

                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Field 'Sub Kelas' -->
                        <div class="form-group">
                            <label for="sub">Sub Kelas (Opsional)</label>
                            <input type="text" class="form-control @error('sub') is-invalid @enderror" id="sub"
                                name="sub" placeholder="Contoh: A / B / Unggulan" value="{{ old('sub', $kelas->sub) }}">
                            @error('sub')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
