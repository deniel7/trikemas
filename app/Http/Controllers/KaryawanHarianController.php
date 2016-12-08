<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use App\Karyawan;
use App\StatusKaryawan;
use Datatables;

class KaryawanHarianController extends Controller
{
    public function index()
    {
        return view('karyawan_harian.index');
    }

    public function datatable()
    {
        $karyawan_harians = DB::table('karyawans')
        ->select(['karyawans.id', 'karyawans.status_karyawan_id', 'status_karyawans.keterangan', 'karyawans.nik', 'karyawans.nama', 'karyawans.alamat', 'karyawans.phone', 'karyawans.lulusan', 'karyawans.tgl_masuk', 'karyawans.nilai_upah', 'karyawans.uang_makan', 'karyawans.uang_lembur', 'karyawans.norek'])
        ->join('status_karyawans', 'karyawans.status_karyawan_id', '=', 'status_karyawans.id')
        ->where('karyawans.status_karyawan_id', '=', 2);

        return Datatables::of($karyawan_harians)

        ->editColumn('status_karyawan_id', '<span class="pull-right">{{ App\Karyawan::find($id)->statusKaryawan->keterangan }}</span>')
        ->editColumn('nama', '<span class="pull-right">{{ $nama }}</span>')
        ->editColumn('alamat', '<span class="pull-right">{{ $alamat }}</span>')
        ->editColumn('phone', '<span class="pull-right">{{ $phone }}</span>')
        ->editColumn('lulusan', '<span class="pull-right">{{ $lulusan }}</span>')
        ->editColumn('tgl_masuk', '<span class="pull-right">{{ $tgl_masuk }}</span>')
        ->editColumn('nilai_upah', '<span class="pull-right">{{ $nilai_upah }}</span>')
        ->editColumn('uang_makan', '<span class="pull-right">{{ $uang_makan }}</span>')
        ->editColumn('uang_lembur', '<span class="pull-right">{{ $uang_lembur }}</span>')
        ->editColumn('norek', '<span class="pull-right">{{ $norek }}</span>')
        ->addColumn('action', function ($karyawan_harian) {

            $html = '<div class="text-center btn-group btn-group-justified">';

            $html .= '<a href="karyawan-tetap/'.$karyawan_harian->id.'/edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a>';
            $html .= '<a href="javascript:;" onClick="karyawanHarianModule.showPrint('.$karyawan_harian->id.');"><button type="button" class="btn btn-sm"><i class="fa fa-print"></i></button></a>';
            $html .= '</div>';

            return $html;
        })

        ->make(true);
    }

    public function show($karyawan_harian)
    {
        $id = $karyawan_harian->id;

        $details = DB::table('karyawans')
        ->select('karyawans.id', 'nik', 'nama', 'norek', 'status_karyawans.keterangan')
        ->join('status_karyawans', 'status_karyawans.id', '=', 'karyawans.status_karyawan_id')
        ->where('karyawans.id', '=', $id)
        ->get();

        // $test = Karyawan::find($id);
        if (count($details) == 1) {
            return response()->json([
                'status' => 1,
                'records' => $details,
                ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Failed',
                ]);
        }
    }
}
