@extends('layouts.admin.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Kriteria & Pembobotan (ROC)</h4>
                    <p class="card-description">
                        Berikut adalah daftar <span class="text-success font-weight-bold"> Kriteria dan Bobot (ROC) </span> yang terdaftar di
                        sistem.
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Prioritas</th>
                                    <th>Nama Kriteria</th>
                                    <th>Bobot (ROC)</th>
                                    <th>Jenis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kriteria as $k)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $k->prioritas }}</span>
                                        </td>
                                        <td>{{ $k->nama }}</td>
                                        <td>
                                            {{ number_format($k->bobot, 4) }}
                                        </td>
                                        <td>
                                            {{ $k->jenis == 'benefit' ? 'Benefit' : 'Cost' }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2 text-muted text-small">
                        Total Bobot saat ini: <strong>{{ $kriteria->sum('bobot') }}</strong> (Harus samadengan 1).
                        Kriteria dan perhitungan bobot dilakukan oleh BK.
                    </div>

                    <div class="mt-4">{{ $kriteria->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
