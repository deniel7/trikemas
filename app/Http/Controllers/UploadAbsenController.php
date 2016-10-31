<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use App\Karyawan;

class UploadAbsenController extends Controller
{
    public function show($id)
    {
        // $details = DB::table('karyawans')
        // ->select(['karyawans.id', 'karyawans.nama', 'karyawans.nik'])
        // ->where('karyawans.id', '=', $id)
        // ->get();

        $details = Karyawan::select(['karyawans.id', 'karyawans.nik', 'karyawans.nama', 'status_karyawans.keterangan'])
        ->join('status_karyawans', 'karyawans.status_karyawan_id', '=', 'status_karyawans.id')
        ->where('karyawans.id', '=', $id)
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
}
