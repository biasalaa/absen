<?php

namespace App\Http\Controllers;


use App\Imports\SiswaImport;
use App\Exports\SiswaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;

class SiswaController extends Controller
{
    public function index()
    {
        Paginator::useBootstrap();
        $siswa = DB::table('siswa')
            ->select('jurusan', 'siswa.*', 'nama_ruangan')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            ->join('ruangan', 'siswa.id_ruangan', 'ruangan.id')
            ->simplePaginate(10);
        $jurusan = DB::table('jurusan')->get();
        $ruangan = DB::table('ruangan')->get();
        return view('dashboard.siswa', compact('siswa', 'jurusan', 'ruangan'));
    }

    public function export()
    {
        return Excel::download(new SiswaExport, 'siswa.xlsx');
    }

    public function ImportSiswaExcel(Request $request)
    {
        request()->validate([
            'file' => 'required|mimes:xls,xlsx',
        ], [
            'file.required' => 'Harap di isi',
            'file.mimes' => 'Tidak support',
        ]);

        $file = $request->file('file');
        $nama_file = Rand(1, 30) . $file->getClientOriginalName();
        $file->move(public_path('Excel'), $nama_file);

        Excel::import(new SiswaImport, public_path('Excel/' . $nama_file));

        return redirect()->back()->with('success', 'siswa berhasil import');
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswa,nisn|max:10',
            'kelas' => 'required',
            'no_kelas' => 'required',
            'jurusan' => 'required',
            'sesi' => 'required',
            'id_ruangan' => 'required'
        ], [
            'nama.required' => 'nama tidak boleh kosong',
            'nisn.required' => 'nisn tidak boleh kosong',
            'nisn.max:10' => 'nisn harus pas 10',
            'nisn.unique' => 'nisn sudah terdaftar',
            'jurusan.required' => 'jurusan tidak boleh kosong',
            'kelas.required' => 'kelas tidak boleh kosong',
            'no_kelas.required' => 'no_kelas tidak boleh kosong',
            'sesi.required' => 'sesi tidak boleh kosong',
            'id_ruangan.required' => 'ruangan tidak boleh kosong'
        ]);

        $nama = Str::upper($request->nama);
        $nisn = $request->nisn;
        $kelas = $request->kelas;
        $no_kelas = $request->no_kelas;
        $jurusan = $request->jurusan;
        $sesi = $request->sesi;
        $nama_ruangan = $request->nama_ruangan;


        DB::table('siswa')->insert([
            'nama' => $nama,
            'nisn' => $nisn,
            'kelas' => $kelas,
            'no_kelas' => $no_kelas,
            'sesi' => $sesi,
            'id_jurusan' => $jurusan,
            'id_ruangan' => $nama_ruangan
        ]);

        return redirect()->back()->with('success', 'siswa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswa,nisn|max:10',
            'kelas' => 'required',
            'no_kelas' => 'required',
            'jurusan' => 'required',
            'sesi' => 'required',
            'id_ruangan' => 'required'
        ], [
            'nama.required' => 'nama tidak boleh kosong',
            'nisn.required' => 'nisn tidak boleh kosong',
            'nisn.max:10' => 'nisn harus pas 10',
            'nisn.unique' => 'nisn sudah terdaftar',
            'jurusan.required' => 'jurusan tidak boleh kosong',
            'kelas.required' => 'kelas tidak boleh kosong',
            'no_kelas.required' => 'no_kelas tidak boleh kosong',
            'sesi.required' => 'sesi tidak boleh kosong',
            'id_ruangan.required' => 'ruangan tidak boleh kosong'
        ]);

        $nama = Str::upper($request->nama);
        $nisn = $request->nisn;
        $kelas = $request->kelas;
        $no_kelas = $request->no_kelas;
        $jurusan = $request->jurusan;
        $sesi = $request->sesi;
        $id_ruangan = $request->id_ruangan;


        DB::table('siswa')->where('id', $id)->update([
            'nama' => $nama,
            'nisn' => $nisn,
            'kelas' => $kelas,
            'no_kelas' => $no_kelas,
            'sesi' => $sesi,
            'id_jurusan' => $jurusan,
            'id_ruangan' => $id_ruangan
        ]);

        return redirect()->back()->with('success', 'siswa berhasil diubah');
    }

    public function destroy($id)
    {
        DB::table('siswa')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'jurusan berhasil di hapus');
    }
}
