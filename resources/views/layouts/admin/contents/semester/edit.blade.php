@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Semester</h4>
                    <form class="forms-sample" action="{{ route('proses.semester.update', $semester->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="form-group">
                            <label for="nama">Nama Semester</label>
                            <select class="form-control" name="nama" id="nama">
                                <option value="Ganjil" {{ $semester->nama == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ $semester->nama == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tahun_mulai">Tahun Mulai</label>
                            <input type="number" class="form-control" name="tahun_mulai"
                                value="{{ $semester->tahun_mulai }}" required>
                        </div>

                        <div class="form-group">
                            <label for="tahun_selesai">Tahun Selesai</label>
                            <input type="number" class="form-control" name="tahun_selesai"
                                value="{{ $semester->tahun_selesai }}" required>
                        </div>


                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a href="{{ route('proses.semester.index') }}" class="btn btn-light">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
