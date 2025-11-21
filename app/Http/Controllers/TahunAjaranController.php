<?php

namespace App\Http\Controllers\Admin;

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::latest()->paginate(10);
        return view('layouts.admin.contents.tahun-ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layouts.admin.contents.tahun-ajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_mulai' => 'required|digits:4|integer|min:1900',
            // Pastikan tahun selesai lebih besar atau sama dengan tahun mulai
            'tahun_selesai' => 'required|digits:4|integer|min:1900|gte:tahun_mulai',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        TahunAjaran::create($request->only(['tahun_mulai', 'tahun_selesai', 'status']));

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunAjaran) // Route Model Binding
    {
        return view('layouts.admin.contents.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun_mulai' => 'required|digits:4|integer|min:1900',
            // Pastikan tahun selesai lebih besar atau sama dengan tahun mulai
            'tahun_selesai' => 'required|digits:4|integer|min:1900|gte:tahun_mulai',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Jika status baru adalah 'aktif', nonaktifkan semua yang lain
        if ($request->status == 'aktif') {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['status' => 'nonaktif']);
        }

        $tahunAjaran->update($request->only(['tahun_mulai', 'tahun_selesai', 'status']));

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        // Tambahkan proteksi agar tidak menghapus jika masih punya semester
        if ($tahunAjaran->semester()->count() > 0) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Gagal! Tahun ajaran ini masih memiliki semester terkait.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
