<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Datatables;
use Carbon\Carbon;
use App\AbsensiHarian;
use Illuminate\Http\Request;
use Flash;
use DB;

class AbsensiApprovalController extends Controller
{
    public function index()
    {
        return view('absensi-approval.index');
    }

    public function datatable()
    {
        $absensi_harians = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi'])
        ->leftjoin('karyawans', 'karyawans.id', '=', 'absensi_harians.karyawan_id');

        return Datatables::of($absensi_harians)
        ->addColumn('check', '<input type="checkbox" name="selected_karyawans[]" value="{{ $id_absen }}">')

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

        ->editColumn('pot_absensi', '<span class="pull-right">{{ number_format($pot_absensi,0,".",",") }}</span>')

        ->editColumn('status', function ($absensi_harian) {

            if ($absensi_harian->status == 1) {
                return $absensi_harian->status = 'Need Approval';
            } elseif ($absensi_harian->status == 2) {
                return $absensi_harian->status = 'Approved';
            } else {
                return $absensi_harian->status = 'not Approved';
            }

        })
        ->editColumn('created_at', function ($absensi_harian) {
            return $absensi_harian->created_at ? with(new Carbon($absensi_harian->created_at))->format('d-m-Y') : '';
        })

        ->editColumn('action', '<div class="text-center btn-group btn-group-justified"><a href="javascript:;" onClick="absensiApprovalModule.showDetail({{ $id_absen }});"><button type="button" class="btn btn-sm btn-default"><i class="fa fa-eye"></i></button></a></div>')

        ->make(true);
    }

    public function show(Request $request)
    {
        $absensi_ids = $request->input('selected_karyawans');

        //$absensi_karyawans = AbsensiHarian::where('id', '=', $absensi_ids)->get();

        $absensi_karyawans = AbsensiHarian::whereIn('id', $absensi_ids)->get();

        //dd($data);

        if ($absensi_karyawans->count() > 0) {
            foreach ($absensi_karyawans as $absensi_karyawan) {
                $absensi_karyawan->status = 2;
                $absensi_karyawan->save();
                Flash::success('Absensi Karyawan Confirmed');
            }
        }

        // $data['transactions'] = Transaction::whereIn('id', $transaction_ids)->get();

        return view('absensi-approval.index');
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $tanggal = $request->input('tanggal');
        $potongan = $request->input('potongan');

        $absensies = AbsensiHarian::where('karyawan_id', '=', $id)->where('tanggal', '=', $tanggal)->get();

        if ($absensies->count() == 1) {
            $absensies = $absensies->first();

            $absensies->pot_absensi = $potongan;
            $absensies->status = 2;

            $absensies->save();
        }

        Flash::success('Berhasil input Potongan Absensi');
        DB::commit();

        return redirect('absensi-approval');
    }
}
