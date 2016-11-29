<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AbsensiHarian;
use DB;
use Flash;

class UploadAbsenController extends Controller
{
    public function show($id)
    {
        // $details = DB::table('karyawans')
        // ->select(['karyawans.id', 'karyawans.nama', 'karyawans.nik'])
        // ->where('karyawans.id', '=', $id)
        // ->get();

        $details = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.status'])
        ->join('karyawans', 'karyawans.id', '=', 'absensi_harians.karyawan_id')
        ->where('absensi_harians.id', '=', $id)
        ->get();

        if (count($details) > 0) {
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

    public function update(Request $request)
    {
        $id = $request->input('id');
        $tanggal = $request->input('tanggal');

        $absensies = AbsensiHarian::where('karyawan_id', '=', $id)->where('tanggal', '=', $tanggal)->get();

        if ($absensies->count() == 1) {
            $absensies = $absensies->first();

            $absensies->jam_lembur = $request->input('lembur');
            $absensies->status = 1;

            $absensies->save();
        }

        Flash::success('Berhasil input Jam Lembur');
        DB::commit();

        return redirect('absensi-harian');
    }
}