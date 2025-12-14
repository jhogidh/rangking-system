<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::latest()->paginate(10);

        return view('layouts.admin.contents.kelas.index', ['kelas' => $kelasList]);
    }

    public function create()
    {
        return view('layouts.admin.contents.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kelas,nama|max:100',
        ]);

        Kelas::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('proses.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela)
    {
        return view('layouts.admin.contents.kelas.edit', ['kelas' => $kela]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                Rule::unique('kelas')->ignore($kela->id),
                'max:100'
            ]
        ]);

        $kela->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('proses.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('proses.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
