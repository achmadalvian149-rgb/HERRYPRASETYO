<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
       $siswas = Siswa::with('kelas')->get();
    return view('siswa.index', compact('siswas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
    return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
       $validated = $request->validate([
        'nis'          =>'required|unique:siswas,nis',
        'nama'         =>'required|string|max:255',
        'jenis_kelamin'=>'required',
        'alamat'       =>'required',
        'no_hp'        =>'required',
        'kelas_id'     =>'required|exists:kelas,id',
       ]);
       siswa::create($validated);
       return redirect()->route('siswa.index')->with('success','data siswa berhasil ditambahin boss');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load('kelas', 'nilais');
        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
    return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
         $validated = $request->validate([
        'nis'          =>'required|unique:siswas,nis',
        'nama'         =>'required|string|max:255',
        'jenis_kelamin'=>'required',
        'alamat'       =>'required',
        'no_hp'        =>'required',
        'kelas_id'     =>'required|exists:kelas,id',
       ]);
       $siswa->update($validated);
       return redirect()->route('siswa.index')->with('success','data siswa berhasil diupdate boss');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
       return redirect()->route('siswa.index')->with('success', 'data siswa berhasil dihapus');
    }
}