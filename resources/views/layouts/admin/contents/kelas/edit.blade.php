@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Data Kelas</h4>
                    <p class="card-description">
                        Ubah data <span
                            class="text-success font-weight-bold">Kelas</span> di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('proses.kelas.update', $kelas->id) }}" method="POST">
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


                        <button type="submit" class="btn btn-info mr-2">Update</button>
                        <a href="{{ route('proses.kelas.index') }}" class="btn btn-warning">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
