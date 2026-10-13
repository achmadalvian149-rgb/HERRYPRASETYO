<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NilaiController extends Controller
{
    public function index()
    {
        $nilai = Nilai::with(['siswa.kelas', 'mapel'])->latest()->paginate(10);

        return view('nilai.index', compact('nilai'));
    }

    public function create()
    {
        $siswa = Siswa::orderBy('nama')->get();
        $mapel = Mapel::orderBy('id')->get();

        return view('nilai.create', compact('siswa', 'mapel'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateNilai($request);

        Nilai::create($this->hitungNilai($validated));

        return redirect()->route('nilai.index')
            ->with('success', 'Data nilai berhasil ditambahkan.');
    }

    public function edit(Nilai $nilai)
    {
        $siswa = Siswa::orderBy('nama')->get();
        $mapel = Mapel::orderBy('id')->get();

        return view('nilai.edit', compact('nilai', 'siswa', 'mapel'));
    }

    public function update(Request $request, Nilai $nilai)
    {
        $validated = $this->validateNilai($request);

        $nilai->update($this->hitungNilai($validated));

        return redirect()->route('nilai.index')
            ->with('success', 'Data nilai berhasil diperbarui.');
    }

    public function destroy(Nilai $nilai)
    {
        $nilai->delete();

        return redirect()->route('nilai.index')
            ->with('success', 'Data nilai berhasil dihapus.');
    }

    /**
     * Validasi input nilai (dipakai store & update).
     */
    private function validateNilai(Request $request): array
    {
        return $request->validate([
            'siswa_id'    => ['required', Rule::exists(Siswa::class, 'id')],
            'mapel_id'    => ['required', Rule::exists(Mapel::class, 'id')],
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts'   => 'required|numeric|min:0|max:100',
            'nilai_uas'   => 'required|numeric|min:0|max:100',
        ]);
    }

    /**
     * Hitung nilai akhir dan keterangan berdasarkan KKM mapel.
     * Nilai akhir = (tugas * 0.3) + (uts * 0.3) + (uas * 0.4)
     */
    private function hitungNilai(array $data): array
    {
        $kkm = Mapel::findOrFail($data['mapel_id'])->kkm;

        $nilaiAkhir = ($data['nilai_tugas'] * 0.3)
                    + ($data['nilai_uts'] * 0.3)
                    + ($data['nilai_uas'] * 0.4);

        $data['nilai_akhir'] = round($nilaiAkhir, 2);
        $data['keterangan']  = $data['nilai_akhir'] >= $kkm ? 'Lulus' : 'Tidak Lulus';

        return $data;
    }
}