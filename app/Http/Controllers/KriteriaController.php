<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('prioritas', 'asc')->paginate(10);
        return view('layouts.admin.contents.kriteria.index', compact('kriteria'));
    }

    public function indexGuru()
    {
        $kriteria = Kriteria::orderBy('prioritas', 'asc')->paginate(10);
        return view('layouts.admin.contents.kriteria_guru.index', compact('kriteria'));
    }

    public function create()
    {
        return view('layouts.admin.contents.kriteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kriteria,nama|max:255',
            'prioritas' => 'required|integer|min:1|unique:kriteria,prioritas',
        ]);

        Kriteria::create([
            'nama' => $request->nama,
            'prioritas' => $request->prioritas,
            'bobot' => 0,
            'jenis' => $request->jenis
        ]);

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria ditambahkan. Jangan lupa klik "Hitung Bobot ROC"!');
    }

    public function edit(Kriteria $kriteria)
    {
        return view('layouts.admin.contents.kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kriteria')->ignore($kriteria->id),
            ],
            'prioritas' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('kriteria')->ignore($kriteria->id),
            ],
            'jenis' => [
                'required',
            ]
        ]);

        $kriteria->update([
            'nama' => $request->nama,
            'prioritas' => $request->prioritas,
            'jenis' => $request->jenis
        ]);

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Data kriteria diperbarui. Silakan hitung ulang bobot.');
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Data kriteria dihapus. Silakan hitung ulang bobot.');
    }

    public function hitungBobot()
    {
        $semuaKriteria = Kriteria::orderBy('prioritas', 'asc')->get();
        $totalKriteria = $semuaKriteria->count();

        if ($totalKriteria == 0) {
            return back()->with('error', 'Belum ada data kriteria.');
        }

        foreach ($semuaKriteria as $index => $kriteria) {

            $rank = $index + 1;
            $sigma = 0;

            for ($i = $rank; $i <= $totalKriteria; $i++) {
                $sigma += (1 / $i);
            }

            $bobotRoc = $sigma / $totalKriteria;

            $kriteria->update(['bobot' => $bobotRoc]);
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Bobot berhasil dihitung ulang menggunakan metode ROC!');
    }
}
