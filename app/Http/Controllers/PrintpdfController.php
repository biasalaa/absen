<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Models\printsiswaModel;
use App\Exports\AbsenExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PrintpdfController extends Controller
{
    public function index()
    {
        return view('dashboard.print');
    }

    public function print()
    {

        $html =  view('dashboard.printpdf' );

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render('dashboard.printpdf');

        // Output the generated PDF to Browser
        $dompdf->stream('dashboard.printpdf');
        // return view('dashboard.printpdf');
    }

    public function printSiswaUi(Request $request)
    {
        $siswa = DB::table('siswa')
            ->select('siswa.*', 'jurusan')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            ->get();
        $data = DB::table('absen')
        ->select('siswa.id','nisn','nama','no_kelas','jurusan','kelas','status')
            ->rightJoin('siswa', 'absen.id_siswa', 'siswa.id')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            // ->select('absen.*', 'nisn', 'nama', 'no_kelas', 'kelas', 'jurusan')
            ->get();
        // dd($data);
            
        $jurusan = DB::table('jurusan')->get();
        return view('dashboard.printSiswa', compact('jurusan', 'data'));
    }

    public function filter(Request $request)
    {
        $absen = printsiswaModel::all();
        $siswa = DB::table('siswa')
            ->select('siswa.*', 'jurusan')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            ->get();
            // dd($request);
        $data = DB::table('absen')
            ->rightJoin('siswa', 'absen.id_siswa', 'siswa.id')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            ->select('absen.*','nama', 'nisn', 'no_kelas', 'kelas', 'jurusan')
            ->where('siswa.kelas', $request->kelas)
            ->Where('siswa.no_kelas', $request->no_kelas)
            ->Where('jurusan.jurusan', $request->jurusan)
            ->get();

        $jurusan = DB::table('jurusan')->get();
        $ruang = DB::table('ruangan')->get();
        return view('dashboard.printSiswa', compact('jurusan', 'data','ruang'));
    }

    // export siswa
    public function export(Request $request)
    {
        $siswa = DB::table('siswa')
            ->select('siswa.*', 'jurusan')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            ->get();
            // dd($request);
        $data = DB::table('absen')
            ->Join('siswa', 'absen.id_siswa', 'siswa.id')
            ->join('jurusan', 'siswa.id_jurusan', 'jurusan.id')
            ->select('absen.*','nama', 'nisn', 'no_kelas', 'kelas', 'jurusan')
            ->where('siswa.kelas', $request->kelas)
            ->Where('siswa.no_kelas', $request->no_kelas)
            ->Where('jurusan.jurusan', $request->jurusan)
            ->get();
            
        $jurusan = DB::table('jurusan')->get();
        $ruang = DB::table('ruangan')->get();
        $guru = DB::table('guru')->get();
        return Excel::download(new absenExport, 'absenSiswa.xlsx');
    }

    // update status
    public function printSiswa(Request $request,$id)
    {
        $request->validate(
            [
                'status' => 'required'
            ],
            [
                'status.required' => 'status wajib di isi'
            ]
        );

        $status = ($request->status);
                    DB::table('absen')->where('id', $id)->update([
            'status' =>(int) $status
        ]);
     

        return redirect()->back()->with('success', 'status berhasil di edit');
    }
}
