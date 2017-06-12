<?php

// use DB;
use App\AbsensiHarian;

function getAbsenExcel($file, $status_karyawan_id)
{
    $tanggal = '';
    Excel::selectSheetsByIndex(0)->load($file, function ($reader) use ($status_karyawan_id) {

                $reader->skip(2);
                $reader->noHeading();

                $rows = $reader->all();

        foreach ($rows as $row) {
            $tanggal = $row[5];
            break;
        }

        $karyawan_absens = DB::table('karyawans')
        ->where('karyawans.status_karyawan_id', '=', $status_karyawan_id)
        ->get();

        foreach ($karyawan_absens as $karyawan_absen) {
            $found = false;
            foreach ($rows as $row) {
                if ($karyawan_absen->nik == intval($row[1])) {
                    //dd($karyawan_absen->nik);
                    $record = new AbsensiHarian();

                    $record->tanggal = $tanggal;
                    $record->karyawan_id = strval($row[1]);
                    $record->jam_masuk = strval($row[7]);
                    $record->jam_pulang = strval($row[8]);
                    $record->jam_kerja = strval($row[6]);
                    $record->scan_masuk = strval($row[9]);
                    $record->scan_pulang = strval($row[10]);
                    $record->terlambat = strval($row[11]);
                    $record->jam_lembur = strval($row[13]);
                    $record->plg_cepat = strval($row[12]);
                    $record->jml_jam_kerja = strval($row[14]);
                    $record->departemen = strval($row[16]);
                    $record->jml_kehadiran = strval($row[18]);

                    $ambil_jam_kerja = substr(strval($row[14]), -8);
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
                    if (empty($record->karyawan_id)) {
                        return;
                    }

                    $record->jam = $jam;
                    $record->menit = $menit;

                    $record->save();
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Isi null
                $record = new AbsensiHarian();
                $record->tanggal = $tanggal;
                $record->karyawan_id = $karyawan_absen->nik;
                $record->jam_masuk = null;

                    // ..

                    $record->save();
            }
        }

    })->toObject();
}

function getLemburExcel($file)
{
    $tanggal = '';
    Excel::selectSheetsByIndex(0)->load($file, function ($reader) {

                $reader->skip(2);
                $reader->noHeading();

                $rows = $reader->all();

        foreach ($rows as $row) {
            $tanggal = $row[5];
            break;
        }

        $karyawan_absens = DB::table('karyawans')
        ->get();

        foreach ($karyawan_absens as $karyawan_absen) {
            foreach ($rows as $row) {
                if ($karyawan_absen->nik == intval($row[1])) {
                    //dd($karyawan_absen->nik);
                    $record = new AbsensiHarian();

                    $record->tanggal = $tanggal;
                    $record->karyawan_id = strval($row[1]);
                    $record->jam_masuk = strval($row[7]);
                    $record->jam_pulang = strval($row[8]);
                    $record->jam_kerja = strval($row[6]);
                    $record->scan_masuk = strval($row[9]);
                    $record->scan_pulang = strval($row[10]);
                    $record->terlambat = strval($row[11]);
                    $record->jam_lembur = strval($row[13]);
                    $record->plg_cepat = strval($row[12]);
                    $record->jml_jam_kerja = strval($row[14]);
                    $record->departemen = strval($row[16]);
                    $record->jml_kehadiran = strval($row[18]);

                    $ambil_jam_kerja = substr(strval($row[14]), -8);
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
                    if (empty($record->karyawan_id)) {
                        return;
                    }

                    $record->jam = $jam;
                    $record->menit = $menit;

                    $record->save();
                    break;
                }
            }
        }

    })->toObject();
}
