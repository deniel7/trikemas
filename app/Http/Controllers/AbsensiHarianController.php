<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Datatables;
use Carbon\Carbon;
use App\AbsensiHarian;
use Excel;
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
        $absensi_harians = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status'])
        ->leftjoin('karyawans', 'karyawans.id', '=', 'absensi_harians.karyawan_id');

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
            Excel::selectSheetsByIndex(0)->load($file, function ($reader) {

                $reader->skip(2);
                $reader->noHeading();

                $rows = $reader->all();

                foreach ($rows as $row) {
                    $tanggal = $row[6];
                    $karyawan_id = strval($row[2]);
                    $jam_masuk = strval($row[8]);
                    $jam_pulang = strval($row[9]);
                    $jam_kerja = strval($row[7]);

                    $scan_masuk = strval($row[10]);
                    $scan_pulang = strval($row[11]);
                    $terlambat = strval($row[12]);
                    $plg_cepat = strval($row[13]);
                    $lembur = strval($row[14]);
                    $jml_jam_kerja = strval($row[15]);
                    $departemen = strval($row[17]);
                    $jml_kehadiran = strval($row[19]);

                    $ambil_jam_kerja = substr($jml_jam_kerja, -8);

                    $jam_exp = explode(':', $ambil_jam_kerja);

                    $jam = isset($jam_exp[0]) ? $jam_exp[0] : '';

                    $menit = isset($jam_exp[1]) ? $jam_exp[1] : '';

                    //pembulatan jam dan menit jika terlambat

                    if ($jam == 7) {
                        if ($menit > 44) {
                            $jam = 8;
                            $menit = 00;
                        }
                    }

                    if (empty($karyawan_id)) {
                        return;
                    }

                    /* Cek Apakah Ada karyawan Tersebut */
                    $karyawans = AbsensiHarian::where('karyawan_id', '=', $karyawan_id)->where('tanggal', '=', $tanggal)->get();

                    $c = $karyawans->count();

                    if ($c != 1) {
                        $record = new AbsensiHarian();
                        $record->tanggal = $tanggal;
                        $record->karyawan_id = $karyawan_id;
                        $record->jam_masuk = $jam_masuk;
                        $record->jam_pulang = $jam_pulang;
                        $record->jam_kerja = $jam_kerja;
                        $record->scan_masuk = $scan_masuk;
                        $record->scan_pulang = $scan_pulang;
                        $record->terlambat = $terlambat;
                        $record->jam_lembur = $lembur;
                        $record->plg_cepat = $plg_cepat;
                        $record->jml_jam_kerja = $jml_jam_kerja;
                        $record->departemen = $departemen;
                        $record->jml_kehadiran = $jml_kehadiran;
                        $record->jam = $jam;
                        $record->menit = $menit;
                        $record->save();
                        Flash::success('success');
                    } else {
                        Flash::error('Proses import  karyawan STAFF ada kesalahan');
                    }
                }
            })->toObject();
        } else {
            Flash::error('File karyawan STAFF belum dipilih');
        }

        if (!empty($file2)) {
            Excel::selectSheetsByIndex(0)->load($file, function ($reader) {

                $reader->skip(2);
                $reader->noHeading();

                $rows = $reader->all();

                foreach ($rows as $row) {
                    $tanggal = $row[6];
                    $karyawan_id = strval($row[2]);
                    $jam_masuk = strval($row[8]);
                    $jam_pulang = strval($row[9]);
                    $jam_kerja = strval($row[7]);

                    $scan_masuk = strval($row[10]);
                    $scan_pulang = strval($row[11]);
                    $terlambat = strval($row[12]);
                    $plg_cepat = strval($row[13]);
                    $lembur = strval($row[14]);
                    $jml_jam_kerja = strval($row[15]);
                    $departemen = strval($row[17]);
                    $jml_kehadiran = strval($row[19]);

                    $ambil_jam_kerja = substr($jml_jam_kerja, -8);

                    $jam_exp = explode(':', $ambil_jam_kerja);

                    $jam = isset($jam_exp[0]) ? $jam_exp[0] : '';

                    $menit = isset($jam_exp[1]) ? $jam_exp[1] : '';

                    if (empty($karyawan_id)) {
                        return;
                    }

                    /* Cek Apakah Ada karyawan Tersebut */
                    $karyawans = AbsensiHarian::where('karyawan_id', '=', $karyawan_id)->where('tanggal', '=', $tanggal)->get();

                    $c = $karyawans->count();

                    if ($c != 1) {
                        $record = new AbsensiHarian();
                        $record->tanggal = $tanggal;
                        $record->karyawan_id = $karyawan_id;
                        $record->jam_masuk = $jam_masuk;
                        $record->jam_pulang = $jam_pulang;
                        $record->jam_kerja = $jam_kerja;
                        $record->scan_masuk = $scan_masuk;
                        $record->scan_pulang = $scan_pulang;
                        $record->terlambat = $terlambat;
                        $record->jam_lembur = $lembur;
                        $record->plg_cepat = $plg_cepat;
                        $record->jml_jam_kerja = $jml_jam_kerja;
                        $record->departemen = $departemen;
                        $record->jml_kehadiran = $jml_kehadiran;
                        $record->jam = $jam;
                        $record->menit = $menit;
                        $record->save();
                        Flash::success('success');
                    } else {
                        Flash::error('Proses import  karyawan STAFF ada kesalahan');
                    }
                }
            })->toObject();
        } else {
            Flash::error('File karyawan KONTRAK belum dipilih');
        }

        if (!empty($file3)) {
            Excel::selectSheetsByIndex(0)->load($file, function ($reader) {

                $reader->skip(2);
                $reader->noHeading();

                $rows = $reader->all();

                foreach ($rows as $row) {
                    $tanggal = $row[6];
                    $karyawan_id = strval($row[2]);
                    $jam_masuk = strval($row[8]);
                    $jam_pulang = strval($row[9]);
                    $jam_kerja = strval($row[7]);

                    $scan_masuk = strval($row[10]);
                    $scan_pulang = strval($row[11]);
                    $terlambat = strval($row[12]);
                    $plg_cepat = strval($row[13]);
                    $lembur = strval($row[14]);
                    $jml_jam_kerja = strval($row[15]);
                    $departemen = strval($row[17]);
                    $jml_kehadiran = strval($row[19]);

                    $ambil_jam_kerja = substr($jml_jam_kerja, -8);

                    $jam_exp = explode(':', $ambil_jam_kerja);

                    $jam = isset($jam_exp[0]) ? $jam_exp[0] : '';

                    $menit = isset($jam_exp[1]) ? $jam_exp[1] : '';

                    if (empty($karyawan_id)) {
                        return;
                    }

                    /* Cek Apakah Ada karyawan Tersebut */
                    $karyawans = AbsensiHarian::where('karyawan_id', '=', $karyawan_id)->where('tanggal', '=', $tanggal)->get();

                    $c = $karyawans->count();

                    if ($c != 1) {
                        $record = new AbsensiHarian();
                        $record->tanggal = $tanggal;
                        $record->karyawan_id = $karyawan_id;
                        $record->jam_masuk = $jam_masuk;
                        $record->jam_pulang = $jam_pulang;
                        $record->jam_kerja = $jam_kerja;
                        $record->scan_masuk = $scan_masuk;
                        $record->scan_pulang = $scan_pulang;
                        $record->terlambat = $terlambat;
                        $record->jam_lembur = $lembur;
                        $record->plg_cepat = $plg_cepat;
                        $record->jml_jam_kerja = $jml_jam_kerja;
                        $record->departemen = $departemen;
                        $record->jml_kehadiran = $jml_kehadiran;
                        $record->jam = $jam;
                        $record->menit = $menit;
                        $record->save();
                        Flash::success('success');
                    } else {
                        Flash::error('Proses import  karyawan STAFF ada kesalahan');
                    }
                }
            })->toObject();
        } else {
            Flash::error('File karyawan HARIAN belum dipilih');
        }

        return redirect('absensi-harian');
    }
}
