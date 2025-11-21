@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Semester</h4>
                    <p class="card-description">
                        Ubah data di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.semester.update', $semester->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="id_tahun_ajaran">Tahun Ajaran</label>
                            <select class="form-control @error('id_tahun_ajaran') is-invalid @enderror" id="id_tahun_ajaran"
                                name="id_tahun_ajaran" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ old('id_tahun_ajaran', $semester->id_tahun_ajaran) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun_mulai }} / {{ $ta->tahun_selesai }} ({{ $ta->status }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tahun_ajaran')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama Semester</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Contoh: Ganjil" value="{{ old('nama', $semester->nama) }}"
                                required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a href="{{ route('admin.semester.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
