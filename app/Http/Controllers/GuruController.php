<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::latest()->paginate(10);

        return view('guru.index', compact('guru'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'           => 'required|string|max:30|unique:guru,nip',
            'nama'          => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'email'         => 'required|email|max:100|unique:guru,email',
            'no_hp'         => 'required|string|max:20',
            'alamat'        => 'required|string',
        ]);

        Guru::create($validated);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip'           => 'required|string|max:30|unique:guru,nip,' . $guru->id,
            'nama'          => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'email'         => 'required|email|max:100|unique:guru,email,' . $guru->id,
            'no_hp'         => 'required|string|max:20',
            'alamat'        => 'required|string',
        ]);

        $guru->update($validated);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}