<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Datatables;
use Carbon\Carbon;
use App\AbsensiHarian;
use App\Karyawan;
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
        $absensi_harians = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'absensi_harians.karyawan_id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi'])
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id');

        return Datatables::of($absensi_harians)
        ->addColumn('check', '<input type="checkbox" name="selected_karyawans[]" value="{{ $id_absen }}-{{ $nik }}" >')

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
        //$c = explode('-', $absensi_ids);

        foreach ($absensi_ids as $id) {
            $a = explode('-', $id);

            //dd($absensi_ids);
            //dd($a[0].'dan '.$a[1]);
        }

        $absensi_karyawans = AbsensiHarian::whereIn('id', $absensi_ids)->get();

        if ($absensi_karyawans->count() > 0) {
            foreach ($absensi_karyawans as $absensi_karyawan) {
                //dd($absensi_karyawan->karyawan_id);
                $karyawan = Karyawan::where('nik', '=', $absensi_karyawan->karyawan_id)->get();
                $karyawan = $karyawan->first();

                $absensi_karyawan->status = 2;
                $absensi_karyawan->save();
                Flash::success('Absensi Karyawan Confirmed');
            }

            // $data['transactions'] = Transaction::whereIn('id', $transaction_ids)->get();

            return view('absensi-approval.index');
        }
    }

    public function update($id, Request $request)
    {
        $lembur_rutin = 0;
        $lembur_biasa = 0;
        $lembur_off = 0;

        $id_absen = $request->input('id_absen');
        $nik = $request->input('nik');

        $tanggal = $request->input('tanggal');
        $potongan = $request->input('potongan');
        $jenis_lembur = $request->input('jenis_lembur');
        $uang_makan = $request->input('uang_makan');
        $konfirmasi_lembur = $request->input('konfirmasi_lembur');

        $karyawan = Karyawan::where('nik', '=', $nik)->get();
        $karyawan = $karyawan->first();

        $gaji = $karyawan->nilai_upah;

        // // HITUNG UANG MAKAN
        $uang_makan = $karyawan->uang_makan;

        $absensies = AbsensiHarian::where('karyawan_id', '=', $nik)->where('tanggal', '=', $tanggal)->get();

        if ($absensies->count() == 1) {
            $absensies = $absensies->first();

            // PENJUMLAHAN POTONGAN ABSENSI
            $absensies->pot_absensi = $potongan;
            $absensies->status = 2;

            // PERHITUNGAN TOTAL

            //karyawan KONTRAK tetap / bulanan
            if ($karyawan->status_karyawan_id == 1) {
                $upah_harian = $absensies->upah_harian - $potongan;

            // karyawan harian / lepas
            } elseif ($karyawan->status_karyawan_id == 2) {
                $upah_harian = $absensies->upah_harian - $potongan;

            //karyawan Staff
            } elseif ($karyawan->status_karyawan_id == 3) {
                $upah_harian = $absensies->upah_harian - $potongan;
            }

            $absensies->upah_harian = $upah_harian;

            $absensies->save();
        }

        Flash::success('Berhasil input Potongan Absensi');
        DB::commit();

        return redirect('absensi-approval');
    }
}
