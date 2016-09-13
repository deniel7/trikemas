<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;

class KaryawanTetapController extends Controller
{
    public function index()
    {
        return view('karyawan.index');
    }

    public function datatable()
    {
        $karyawans = DB::table('karyawans')
        ->select(['karyawans.id', 'karyawans.status_karyawan_id', 'karyawans.nik', 'karyawans.nama', 'karyawans.alamat', 'karyawans.phone', 'karyawans.lulusan', 'karyawans.tgl_masuk', 'karyawans.nilai_upah', 'karyawans.uang_makan', 'karyawans.uang_lembur', 'karyawans.norek']);

        return Datatables::of($karyawans)
        ->editColumn('status_karyawan_id', '{{ App\Item::find($id)->status-karyawans->keterangan }}')
        ->editColumn('nik', '<span class="pull-right">{{ $nik }}</span>')
        ->editColumn('nama', '<span>{{ $nama }}</span>')
        ->editColumn('alamat', '<span>{{ $alamat }}</span>')
        ->editColumn('phone', '<span class="pull-right">{{ $phone }}</span>')
        ->editColumn('lulusan', '{{ $lulusan }}')
        ->editColumn('tgl_masuk', function ($karyawan) {
            return $karyawan->tgl_masuk ? with(new Carbon($item->tgl_masuk))->format('d-m-Y') : '';
        })
        ->editColumn('nilai_upah', '<span>{{ $nilai_upah }}</span>')
        ->editColumn('uang_makan', '<span>{{ $uang_makan }}</span>')
        ->editColumn('uang_lembur', '<span>{{ $uang_lembur }}</span>')
        ->editColumn('norek', '<span>{{ $norek }}</span>')

        ->editColumn('created_at', function ($karyawan) {
            return $karyawan->created_at ? with(new Carbon($karyawan->created_at))->format('d-m-Y') : '';
        })
        ->editColumn('updated_at', function ($karyawan) {
            return $karyawan->updated_at ? with(new Carbon($karyawan->updated_at))->format('d-m-Y') : '';
        })

        ->addColumn('action', function ($karyawan) {
            $html = '<div style="width: 70px; margin: 0px auto;" class="text-center btn-group btn-group-justified" role="group">';
            $html .= '<a role="button" class="btn btn-warning" href="karyawan/'.$karyawan->id.'/edit"><i class="fa fa-fw fa-pencil"></i> EDIT</a>';
            $html .= '</div>';

            return $html;
        })
        ->make(true);
    }
}
