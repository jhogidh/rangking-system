@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Data Kelas Baru</h4>
                    <p class="card-description">
                        Masukkan data <span
                            class="text-success font-weight-bold">Kelas Baru</span> di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('proses.kelas.store') }}" method="POST">
                        @csrf

                        <!-- Field 'Nama Kelas' -->
                        <div class="form-group">
                            <label for="nama">Nama Kelas</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Contoh: 10A" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-info mr-2">Submit</button>
                        <a href="{{ route('proses.kelas.index') }}" class="btn btn-warning">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
