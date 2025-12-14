<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AkademikController extends Controller
{
    public function index()
    {
        $akademik = Akademik::latest()->paginate(10);

        return view('layouts.admin.contents.akademik.index', compact('akademik'));
    }

    public function create()
    {
        return view('layouts.admin.contents.akademik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:akademik,nama|max:255',
            'kode' => 'required|string|unique:akademik,kode|max:50',
        ]);

        Akademik::create($request->all());

        return redirect()->route('admin.akademik.index')
            ->with('success', 'Data mapel akademik berhasil ditambahkan.');
    }

    public function edit(Akademik $akademik)
    {
        return view('layouts.admin.contents.akademik.edit', compact('akademik'));
    }

    public function update(Request $request, Akademik $akademik)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                Rule::unique('akademik')->ignore($akademik->id),
                'max:255'
            ],
            'kode' => [
                'required',
                'string',
                Rule::unique('akademik')->ignore($akademik->id),
                'max:50'
            ],
        ]);

        $akademik->update($request->all());

        return redirect()->route('admin.akademik.index')
            ->with('success', 'Data mapel akademik berhasil diperbarui.');
    }

    public function destroy(Akademik $akademik)
    {
        $akademik->delete();

        return redirect()->route('admin.akademik.index')
            ->with('success', 'Data mapel akademik berhasil dihapus.');
    }
}
