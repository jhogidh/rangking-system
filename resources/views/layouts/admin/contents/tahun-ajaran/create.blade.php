@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Tahun Ajaran Baru</h4>
                    <p class="card-description">
                        Masukkan data di bawah ini.
                    </p>

                    <form class="forms-sample" action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="tahun_mulai">Tahun Mulai</label>
                            <input type="number" class="form-control @error('tahun_mulai') is-invalid @enderror"
                                id="tahun_mulai" name="tahun_mulai" placeholder="Contoh: 2024"
                                value="{{ old('tahun_mulai') }}" required>
                            @error('tahun_mulai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tahun_selesai">Tahun Selesai</label>
                            <input type="number" class="form-control @error('tahun_selesai') is-invalid @enderror"
                                id="tahun_selesai" name="tahun_selesai" placeholder="Contoh: 2025"
                                value="{{ old('tahun_selesai') }}" required>
                            @error('tahun_selesai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
