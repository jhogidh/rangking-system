<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NonAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class NonAkademikController extends Controller
{
    public function index()
    {
        $nonakademik = Nonakademik::latest()->paginate(10);

        return view('layouts.admin.contents.nonakademik.index', compact('nonakademik'));
    }

    public function create()
    {
        return view('layouts.admin.contents.nonakademik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:nonakademik,nama|max:255',
            'kode' => 'required|string|unique:nonakademik,kode|max:50',
        ]);

        Nonakademik::create($request->all());

        return redirect()->route('admin.nonakademik.index')
            ->with('success', 'Data non-akademik berhasil ditambahkan.');
    }

    public function edit(Nonakademik $nonakademik)
    {
        return view('layouts.admin.contents.nonakademik.edit', compact('nonakademik'));
    }

    public function update(Request $request, Nonakademik $nonakademik)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                Rule::unique('nonakademik')->ignore($nonakademik->id),
                'max:255'
            ],
            'kode' => [
                'required',
                'string',
                Rule::unique('nonakademik')->ignore($nonakademik->id),
                'max:50'
            ],
        ]);

        $nonakademik->update($request->all());

        return redirect()->route('admin.nonakademik.index')
            ->with('success', 'Data non-akademik berhasil diperbarui.');
    }

    public function destroy(Nonakademik $nonakademik)
    {
        if ($nonakademik->nilaiMapel()->count() > 0) {
            return redirect()->route('admin.nonakademik.index')
                ->with('error', 'Gagal! Data ini masih digunakan di tabel nilai siswa.');
        }

        $nonakademik->delete();

        return redirect()->route('admin.nonakademik.index')
            ->with('success', 'Data non-akademik berhasil dihapus.');
    }
}
