<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('siswas')->orderBy('nama_kelas')->paginate(10);

        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'   => ['required', 'string', 'max:50', Rule::unique(Kelas::class, 'nama_kelas')],
            'tingkat'      => 'required|string|max:10',
            'jurusan'      => 'required|string|max:50',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela)
    {
        return view('kelas.edit', ['kelas' => $kela]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas'   => ['required', 'string', 'max:50', Rule::unique(Kelas::class, 'nama_kelas')->ignore($kela->getKey())],
            'tingkat'      => 'required|string|max:10',
            'jurusan'      => 'required|string|max:50',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        $kela->update($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        try {
            $kela->delete();
        } catch (QueryException $e) {
            return redirect()->route('kelas.index')
                ->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa.');
        }

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}