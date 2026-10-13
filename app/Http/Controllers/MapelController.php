<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::orderBy('nama_mapel')->paginate(10);

        return view('mapel.index', compact('mapel'));
    }

    public function create()
    {
        return view('mapel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique(Mapel::class, 'kode_mapel')],
            'nama_mapel' => 'required|string|max:100',
            'kkm'        => 'required|integer|min:0|max:100',
        ]);

        Mapel::create($validated);

        return redirect()->route('mapel.index')
            ->with('success', 'Data mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Mapel $mapel)
    {
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique(Mapel::class, 'kode_mapel')->ignore($mapel->getKey())],
            'nama_mapel' => 'required|string|max:100',
            'kkm'        => 'required|integer|min:0|max:100',
        ]);

        $mapel->update($validated);

        return redirect()->route('mapel.index')
            ->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        try {
            $mapel->delete();
        } catch (QueryException $e) {
            return redirect()->route('mapel.index')
                ->with('error', 'Mata pelajaran tidak bisa dihapus karena masih dipakai di data nilai.');
        }

        return redirect()->route('mapel.index')
            ->with('success', 'Data mata pelajaran berhasil dihapus.');
    }
}