<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AbsensiHarian;
use App\Karyawan;
use DB;
use Flash;

class UploadAbsenController extends Controller
{
    public function show($id)
    {
        $details = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'absensi_harians.karyawan_id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'absensi_harians.upah_harian', 'absensi_harians.jenis_lembur', 'karyawans.nik', 'karyawans.nama', 'karyawans.bagian', 'karyawans.nilai_upah', 'karyawans.uang_makan', 'karyawans.pot_koperasi', 'karyawans.pot_bpjs', 'karyawans.tunjangan', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.status', 'karyawans.nama'])
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
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
        $lembur_rutin = 0;
        $lembur_biasa = 0;
        $lembur_off = 0;

        $id = $request->input('id');
        $nik = $request->input('nik');

        $tanggal = $request->input('tanggal');
        $jenis_lembur = $request->input('jenis_lembur');
        $konfirmasi_lembur = $request->input('lembur');

        $karyawan = Karyawan::where('nik', '=', $nik)->get();
        $karyawan = $karyawan->first();

        $gaji = $karyawan['nilai_upah'];

        // HITUNG UANG MAKAN
        $uang_makan = $karyawan['uang_makan'];

        $absensies = AbsensiHarian::where('karyawan_id', '=', $nik)->where('tanggal', '=', $tanggal)->where('id', '=', $id)->get();

        $lemburs = AbsensiHarian::where('karyawan_id', '=', $nik)->where('tanggal', '=', $tanggal)->where('jam_kerja', '=', 'LEMBUR')->where('id', '=', $id)->get();

        // CEK DISINI, JIKA LEMBUR UPLOAD EXCEL
        if ($lemburs->count() == 1) {
            $lemburs = $lemburs->first();
            // PERHITUNGAN TOTAL

            //karyawan KONTRAK tetap / bulanan
            if ($karyawan->status_karyawan_id == 1) {
                $gaji_harian = $gaji / 30;

                if ($jenis_lembur == 1) {
                    $lembur_rutin = $konfirmasi_lembur * 15479.94;
                    $lembur_biasa = 0;
                    $lembur_off = 0;
                } elseif ($jenis_lembur == 2) {
                    $lembur_biasa = $konfirmasi_lembur * 23219.90;
                    $lembur_rutin = 0;
                    $lembur_off = 0;
                } else {
                    $lembur_off = $konfirmasi_lembur * 30959.87;
                    $lembur_rutin = 0;
                    $lembur_biasa = 0;
                }

                $upah_harian = ($lembur_rutin + $lembur_biasa + $lembur_off);

                // karyawan harian / lepas
            } elseif ($karyawan->status_karyawan_id == 2) {
                // PERHITUNGAN LEMBUR
                if ($jenis_lembur == 1) {
                    $lembur_rutin = $konfirmasi_lembur * 12757.14;
                } elseif ($jenis_lembur == 2) {
                    $lembur_biasa = $konfirmasi_lembur * 19135.71;
                }

                $upah_harian = ($lembur_rutin + $lembur_biasa);

                //karyawan Staff
            } elseif ($karyawan->status_karyawan_id == 3) {
                $gaji_harian = $gaji / 30;

                if ($jenis_lembur == 1) {
                    $lembur_rutin = $konfirmasi_lembur * 15479.94;
                    $lembur_biasa = 0;
                    $lembur_off = 0;
                } elseif ($jenis_lembur == 2) {
                    $lembur_biasa = $konfirmasi_lembur * 23219.90;
                    $lembur_rutin = 0;
                    $lembur_off = 0;
                } else {
                    $lembur_off = $konfirmasi_lembur * 30959.87;
                    $lembur_rutin = 0;
                    $lembur_biasa = 0;
                }

                $upah_harian = ($lembur_rutin + $lembur_biasa + $lembur_off);
            }
            //dd($konfirmasi_lembur);
            $lemburs->status = 1;
            $lemburs->upah_harian = $upah_harian;
            $lemburs->konfirmasi_lembur = $konfirmasi_lembur;
            $lemburs->jenis_lembur = $jenis_lembur;
            $lemburs->save();
        } else {
            // if ($absensies->count() == 1) {
             // JIKA BUKAN LEMBUR UPLOAD EXCEL
            $absensies = $absensies->first();

            // PERHITUNGAN GA MASUK
            if (is_null($absensies->scan_masuk)) {
                //karyawan KONTRAK tetap / bulanan
                if ($karyawan->status_karyawan_id == 1) {
                    $gaji_harian = $gaji / 30;

                    if ($karyawan->tunjangan == 0) {
                        $upah_harian = ($gaji_harian + $uang_makan) - 50000 - $uang_makan;
                    } else {
                        $upah_harian = (($gaji_harian + $uang_makan) - ($karyawan->tunjangan * 0.25)) - $uang_makan;
                    }
                // karyawan harian / lepas
                } elseif ($karyawan->status_karyawan_id == 2) {
                    $upah_harian = 0;

                //karyawan Staff
                } elseif ($karyawan->status_karyawan_id == 3) {
                    $gaji_harian = $gaji / 30;
                    if ($karyawan->tunjangan == 0) {
                        $upah_harian = ($gaji_harian + $uang_makan) - 50000 - $uang_makan;
                    } else {
                        $upah_harian = (($gaji_harian + $uang_makan) - ($karyawan->tunjangan * 0.25)) - $uang_makan;
                    }
                }
            } else {
                // PERHITUNGAN TOTAL

            //karyawan KONTRAK tetap / bulanan
                if ($karyawan->status_karyawan_id == 1) {
                    $gaji_harian = $gaji / 30;

                    if ($jenis_lembur == 1) {
                        $lembur_rutin = $konfirmasi_lembur * 15479.94;
                        $lembur_biasa = 0;
                        $lembur_off = 0;
                    } elseif ($jenis_lembur == 2) {
                        $lembur_biasa = $konfirmasi_lembur * 23219.90;
                        $lembur_rutin = 0;
                        $lembur_off = 0;
                    } else {
                        $lembur_off = $konfirmasi_lembur * 30959.87;
                        $lembur_rutin = 0;
                        $lembur_biasa = 0;
                    }

                    $upah_harian = ($gaji_harian + $uang_makan + $lembur_rutin + $lembur_biasa + $lembur_off);

                    // karyawan harian / lepas
                } elseif ($karyawan->status_karyawan_id == 2) {
                    // PERHITUNGAN LEMBUR
                    // jika absen nya normal yaitu 8 jam, selain itu ga dapet uang makan
                    if ($absensies->jam >= 8) {
                        $gaji_jam = ($gaji / 7) * ($absensies->jam - 1);

                        if ($jenis_lembur == 1) {
                            $lembur_rutin = $konfirmasi_lembur * 12757.14;
                        } elseif ($jenis_lembur == 2) {
                            $lembur_biasa = $konfirmasi_lembur * 19135.71;
                        }

                        $upah_harian = ($gaji_jam + $uang_makan + $lembur_rutin + $lembur_biasa);
                    } else {
                        $gaji_jam = ($gaji / 7) * $absensies->jam;

                        if ($jenis_lembur == 1) {
                            $lembur_rutin = $konfirmasi_lembur * 12757.14;
                        } elseif ($jenis_lembur == 2) {
                            $lembur_biasa = $konfirmasi_lembur * 19135.71;
                        }

                        $upah_harian = ($gaji_jam + $lembur_rutin + $lembur_biasa);
                    }
                    //karyawan Staff
                } elseif ($karyawan->status_karyawan_id == 3) {
                    $gaji_harian = $gaji / 30;

                    if ($jenis_lembur == 1) {
                        $lembur_rutin = $konfirmasi_lembur * 15479.94;
                        $lembur_biasa = 0;
                        $lembur_off = 0;
                    } elseif ($jenis_lembur == 2) {
                        $lembur_biasa = $konfirmasi_lembur * 23219.90;
                        $lembur_rutin = 0;
                        $lembur_off = 0;
                    } else {
                        $lembur_off = $konfirmasi_lembur * 30959.87;
                        $lembur_rutin = 0;
                        $lembur_biasa = 0;
                    }

                    $upah_harian = ($gaji_harian + $uang_makan + $lembur_rutin + $lembur_biasa + $lembur_off);
                }
            }

            //dd($konfirmasi_lembur);
            $absensies->status = 1;
            $absensies->upah_harian = $upah_harian;
            $absensies->konfirmasi_lembur = $konfirmasi_lembur;
            $absensies->jenis_lembur = $jenis_lembur;
            $absensies->save();
        }

        Flash::success('Absensi Karyawan Confirmed');
        DB::commit();

        return redirect('absensi-harian');
    }
}
