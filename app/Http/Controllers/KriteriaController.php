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
        // Urutkan berdasarkan prioritas (1, 2, 3...)
        $kriteria = Kriteria::orderBy('prioritas', 'asc')->paginate(10);
        return view('layouts.admin.contents.kriteria.index', compact('kriteria'));
    }

    public function create()
    {
        return view('layouts.admin.contents.kriteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|unique:kriteria,nama_kriteria|max:255',
            // Validasi prioritas harus angka & unik
            'prioritas' => 'required|integer|min:1|unique:kriteria,prioritas',
        ]);

        // Simpan prioritas saja, bobot biarkan 0 dulu
        Kriteria::create([
            'nama_kriteria' => $request->nama_kriteria,
            'prioritas' => $request->prioritas,
            'bobot' => 0,
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
            'nama_kriteria' => [
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
        ]);

        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'prioritas' => $request->prioritas,
            // Bobot tidak diupdate manual, tetap pakai nilai lama sampai dihitung ulang
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

    /**
     * Logic inti perhitungan ROC
     */
    public function hitungBobot()
    {
        // 1. Ambil semua kriteria diurutkan prioritas 1 sampai akhir
        $semuaKriteria = Kriteria::orderBy('prioritas', 'asc')->get();
        $totalKriteria = $semuaKriteria->count();

        if ($totalKriteria == 0) {
            return back()->with('error', 'Belum ada data kriteria.');
        }

        // 2. Loop untuk menghitung bobot tiap kriteria
        // Rumus ROC: W_k = (1/K) * Sum(1/i) dari i=k sampai K

        foreach ($semuaKriteria as $index => $kriteria) {
            // $index dimulai dari 0, tapi prioritas ranking (k) dimulai dari 1
            // Sebenarnya $kriteria->prioritas sudah ada, tapi lebih aman pakai loop index + 1
            // untuk memastikan urutan 1, 2, 3... teratur saat perhitungan matematikanya.

            $rank = $index + 1; // Ini adalah 'k'
            $sigma = 0;

            // Loop Sigma (Sum)
            for ($i = $rank; $i <= $totalKriteria; $i++) {
                $sigma += (1 / $i);
            }

            $bobotRoc = $sigma / $totalKriteria;

            // 3. Update bobot ke database
            $kriteria->update(['bobot' => $bobotRoc]);
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Bobot berhasil dihitung ulang menggunakan metode ROC!');
    }
}
