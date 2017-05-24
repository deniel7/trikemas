<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Datatables;
use Carbon\Carbon;
use App\AbsensiHarian;
use Illuminate\Http\Request;
use Flash;

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
        $absensi_harians = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'absensi_harians.karyawan_id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status'])
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id');

        return Datatables::of($absensi_harians)

        ->editColumn('status', function ($absensi_harian) {

            if ($absensi_harian->status == 1) {
                return $absensi_harian->status = 'Need Approval';
            } elseif ($absensi_harian->status == 2) {
                return $absensi_harian->status = 'Approved';
            } else {
                return $absensi_harian->status = 'not Approved';
            }

        })

        ->editColumn('jenis_lembur', function ($absensi_harian) {

            if ($absensi_harian->jenis_lembur == 1) {
                return $absensi_harian->jenis_lembur = 'Rutin';
            } elseif ($absensi_harian->jenis_lembur == 2) {
                return $absensi_harian->jenis_lembur = 'Biasa';
            } elseif ($absensi_harian->jenis_lembur == 3) {
                return $absensi_harian->jenis_lembur = 'Off';
            } else {
                return $absensi_harian->jenis_lembur = '-';
            }

        })

        ->editColumn('created_at', function ($absensi_harian) {
            return $absensi_harian->created_at ? with(new Carbon($absensi_harian->created_at))->format('d-m-Y') : '';
        })

        ->editColumn('action', '
            <div class="text-center btn-group btn-group-justified"><a href="javascript:;" onClick="absensiHarianModule.showDetail({{ $id_absen }});"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a></div>

            ')

        ->make(true);
    }

    public function postUpload(Request $request)
    {
        $file = $request->file('file');
        $file2 = $request->file('file2');
        $file3 = $request->file('file3');

        if (!empty($file)) {
            getAbsenExcel($file, 3);

            Flash::success('success');
        } else {
            Flash::error('File karyawan STAFF belum dipilih');
        }

        if (!empty($file2)) {
            getAbsenExcel($file2, 1);
            Flash::success('success');
        } else {
            Flash::error('File karyawan KONTRAK belum dipilih');
        }

        if (!empty($file3)) {
            getAbsenExcel($file3, 2);
            Flash::success('success');
        } else {
            Flash::error('File karyawan HARIAN belum dipilih');
        }

        return redirect('absensi-harian');
    }
}
