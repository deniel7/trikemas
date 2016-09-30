<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;

class AbsensiHarianController extends Controller
{
    public function index()
    {
        return view('absensi-harian.index');
    }

    public function postSetDate()
    {
        return view('absensi-harian.date-result');
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
        ->addColumn('action', function ($karyawan) {
            $html = '<div style="width: 70px; margin: 0px auto;" class="text-center btn-group btn-group-justified" role="group">';
            $html .= '<a role="button" class="btn btn-warning" href="karyawan-tetap/'.$karyawan->id.'/edit"><i class="fa fa-fw fa-pencil"></i> EDIT</a>';
            $html .= '</div>';

            return $html;
        })
        ->make(true);
    }
}
