<?php

namespace app\Http\Controllers;

use DB;
use Carbon\Carbon;
use PDF;
use App\AbsensiHarian;
use App\AbsensiPacking;
use DateTime;
use App\Angkutan;
use App\Karyawan;

// jumlah slip gaji per lembar
const SLIP_PER_PAGE = 6;
const SLIP_PER_PAGE_ALT = 6;

class ReportController extends Controller
{
    /**
     * Displays datatables front end view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

    }

    private function readMonth($month)
    {
        $monthLbl = '';
        switch ($month) {
            case 1:
                $monthLbl = 'Januari';
                break;
            case 2:
                $monthLbl = 'Februari';
                break;
            case 3:
                $monthLbl = 'Maret';
                break;
            case 4:
                $monthLbl = 'April';
                break;
            case 5:
                $monthLbl = 'Mei';
                break;
            case 6:
                $monthLbl = 'Juni';
                break;
            case 7:
                $monthLbl = 'Juli';
                break;
            case 8:
                $monthLbl = 'Agustus';
                break;
            case 9:
                $monthLbl = 'September';
                break;
            case 10:
                $monthLbl = 'Oktober';
                break;
            case 11:
                $monthLbl = 'November';
                break;
            case 12:
                $monthLbl = 'Desember';
                break;
        }

        return $monthLbl;
    }

    // prompt
    public function slipGaji()
    {
        $data['default_date'] = date('d-m-Y');

        return view('report.slip_gaji_params', $data);
    }

    // status karyawan: tetap = 1, harian = 2
    // preview
    public function previewSlipGaji($status, $tanggal, $hingga = '', $potongan = 0)
    {
        if ($hingga == '') {
            $hingga = $tanggal;
        }
        if ($status == 1) {
            $this->slipGajiKaryawanTetap($tanggal, $hingga, $potongan);
        } elseif ($status == 2) {
            $this->slipGajiKaryawanHarian($tanggal, $hingga, $potongan);
        }
    }

    private function slipGajiKaryawanTetap($tanggal, $hingga, $potongan)
    {
        $start_date = DateTime::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
        ;
        $end_date = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
        $potongan = $potongan;

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Print Slip Gaji - Trimitra Kemasindo');
        PDF::SetSubject('Slip Gaji Karyawan');
        PDF::SetKeywords('Slip Gaji Karyawan Trimitra Kemasindo');

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        $format = array(216, 356); // legal paper size
        PDF::AddPage('P', $format);
        PDF::SetMargins(15, 10);
        PDF::setX(15);
        // disable existing columns
        PDF::resetColumns();
        // set columns
        PDF::setEqualColumns(2, 84);

        $karyawans = Karyawan::select('nik')->where('status_karyawan_id', 1)->orderBy('nama')->get();
        for ($i = 0; $i < sizeof($karyawans); $i++) {
            if ($i > 0 && $i % SLIP_PER_PAGE == 0) {
                PDF::AddPage('P', $format);
                PDF::setX(15);
                // disable existing columns
                PDF::resetColumns();
                // set columns
                PDF::setEqualColumns(2, 84);
            }

            $nik = $karyawans[$i]->nik;
            //dd($karyawans[16]);
            $karyawan = DB::table('karyawans')->where('karyawans.nik', '=', $nik)->first();
            
            $gaji = $karyawan->nilai_upah;
            if ($potongan == 'bpjs') {
                $pot_bpjs = $karyawan->pot_bpjs;
            } else {
                $pot_bpjs = 0;
            }

            //HITUNG TOTAL JAM KERJA
            $total_jam = DB::table('absensi_harians')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->sum('jam');

            //HITUNG JUMLAH ABSEN SELURUHNYA (include izin / ga masuk)
            $starts = new Carbon($start_date);
            $ends = new Carbon($end_date);

            $jml_absen = DB::select(DB::raw("SELECT COUNT(id) counter
                            FROM `absensi_harians` where `karyawan_id` = :nik AND `status` = 2 AND (`jam_kerja` !='LEMBUR' OR `jam_kerja` IS NULL) AND `tanggal` between :starts AND :ends
                            "), ['nik' => $nik, 'starts' => $starts, 'ends' => $ends]);

            //HITUNG JUMLAH MASUK KERJA (tidak termasuk izin / ga absen)
            $hari_kerja = DB::table('absensi_harians')
                            ->where('jml_kehadiran', '!=', '00:00:00')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->count('jml_kehadiran');

            //HITUNG JUMLAH TIDAK MASUK KERJA
            
            $hari_off = DB::table('absensi_harians')
                ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                ->whereNull('jml_kehadiran')
                ->whereNull('jam_lembur')
                ->whereBetween('absensi_harians.tanggal', [new Carbon($start_date), new Carbon($end_date)])
                ->where('absensi_harians.status', '=', 2)
                ->where('karyawans.status_karyawan_id', '=', 1)
                ->where('karyawans.nik', '=', $nik)
                ->count('absensi_harians.id');

            //      KARYAWAN BULANAN
            //$nilai_upah = $jml_absen[0]->counter * ($gaji + $karyawan->uang_makan);
            $nilai_upah = $gaji;
            // HITUNG UANG MAKAN
            $uang_makan = $hari_kerja * $karyawan->uang_makan;

            // PERHITUNGAN LEMBUR
            $total_lembur_rutin = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 1)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_rutin = 14200 * $total_lembur_rutin;

            $total_lembur_biasa = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_biasa = 21300 * $total_lembur_biasa;

            $total_lembur_off = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 3)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_off = 28400 * $total_lembur_off;

            //CEK DIA PUNYA TUNJANGAN GA
            if ($karyawan->tunjangan !=0) {
            // PERHITUNGAN POTONGAN JABATAN
                $pot_jabatan = (0.25 * $karyawan->tunjangan) * $hari_off;
                $pot_umk = 0;
            } else {
                // PERHITUNGAN POTONGAN UMK
                $pot_umk = 50000 * $hari_off;
                $pot_jabatan = 0;
            }

            // PENJUMLAHAN POTONGAN ABSENSI
            $total_pot_absensi = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('pot_absensi');

            // PERHITUNGAN TOTAL
            $total = ($nilai_upah + $karyawan->tunjangan + $lembur_rutin + $uang_makan + $lembur_biasa + $lembur_off) - ($pot_jabatan + $pot_umk + $total_pot_absensi + $pot_bpjs + $karyawan->pot_koperasi);
            //$total = 0;

            PDF::SetFont('times', 'B', 10);

            PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 9);
            PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15, Bandung', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::Cell(0, 0, 'Tlp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);

            PDF::Cell(40, 0, 'Periode (bulanan):', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' '.$start_date.' s/d '.$end_date, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Tanggal Cetak:', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' ' . date('d-m-Y h:m'), 0, 0, 'L', 0, '', 0);
            PDF::Ln(5);

            PDF::Cell(40, 0, 'Nama:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->nama, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Bagian:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->bagian, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Upah:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($nilai_upah, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Tunj. Jab.:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($karyawan->tunjangan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Uang Makan:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($uang_makan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(5);

            PDF::Cell(40, 0, 'Lbr. Rutin:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_rutin, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lbr. Biasa:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_biasa, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lembur Off:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_off, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(5);

            PDF::Cell(40, 0, 'Potongan Jab.:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_jabatan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Potongan Umk:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_umk, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Potongan Lain Lain:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total_pot_absensi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Potongan BPJS:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_bpjs, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Potongan Koperasi:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($karyawan->pot_koperasi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(6);

            PDF::SetFont('times', 'B', 9);
            PDF::Cell(40, 0, 'Total:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);
            PDF::SetFont('times', '', 9);

            PDF::Cell(0, 0, 'Penerima', 0, 0, 'L', 0, '', 0);
            PDF::Ln(10);
            PDF::Cell(0, 0, '(               )', 0, 0, 'L', 0, '', 0);
            PDF::Ln(12);
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('slip_gaji.pdf');

        // need to call exit, i don't know why
        exit;
    }

    private function slipGajiKaryawanHarian($tanggal, $hingga, $potongan)
    {
        $start_date = DateTime::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
        ;
        $end_date = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
        $potongan = $potongan;

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Print Slip Gaji - Trimitra Kemasindo');
        PDF::SetSubject('Slip Gaji Karyawan');
        PDF::SetKeywords('Slip Gaji Karyawan Trimitra Kemasindo');

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        $format = array(216, 356); // legal paper size
        PDF::AddPage('P', $format);
        PDF::SetMargins(15, 10);
        PDF::setX(15);
        // disable existing columns
        PDF::resetColumns();
        // set columns
        PDF::setEqualColumns(2, 84);

        $karyawans = Karyawan::select('nik')->where('status_karyawan_id', 2)->orderBy('nama')->get();
        for ($i = 0; $i < sizeof($karyawans); $i++) {
            if ($i > 0 && $i % SLIP_PER_PAGE_ALT == 0) {
                PDF::AddPage('P', $format);
                PDF::setX(15);
                // disable existing columns
                PDF::resetColumns();
                // set columns
                PDF::setEqualColumns(2, 84);
            }

            $nik = $karyawans[$i]->nik;

            $karyawan = DB::table('karyawans')->where('karyawans.nik', '=', $nik)->first();
            $gaji = $karyawan->nilai_upah;
            if ($potongan == 'bpjs') {
                $pot_bpjs = $karyawan->pot_bpjs;
            } else {
                $pot_bpjs = 0;
            }

            //HITUNG JUMLAH KERJA
            $hari_kerja = DB::table('absensi_harians')
                            ->where('jml_kehadiran', '!=', '00:00:00')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereNotNull('absensi_harians.jml_jam_kerja')
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->count('jml_kehadiran');

            // HITUNG UANG MAKAN
            $uang_makan = $hari_kerja * $karyawan->uang_makan;

            // PERHITUNGAN LEMBUR
            $total_lembur_rutin = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 1)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_rutin = $total_lembur_rutin * 11700;

            $total_lembur_biasa = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_biasa = $total_lembur_biasa * 17600;

            // PENJUMLAHAN POTONGAN ABSENSI
            $total_pot_absensi = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('pot_absensi');

            $total_upah_harian = DB::table('absensi_harians')
                                    ->select('absensi_harians.pot_absensi')
                                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                                    ->whereBetween('absensi_harians.tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->where('absensi_harians.status', '=', 2)
                                    ->where('karyawans.nik', '=', $nik)
                                    ->sum('absensi_harians.upah_harian');

            // dd($total_upah_harian);
            $nilai_upah = $total_upah_harian + $total_pot_absensi - ($uang_makan + $lembur_rutin + $lembur_biasa);

            // PERHITUNGAN TOTAL
            $total = $nilai_upah + $uang_makan + $lembur_rutin + $lembur_biasa - ($pot_bpjs + $total_pot_absensi);

            PDF::SetFont('times', 'B', 10);

            PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 9);
            PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15, Bandung', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::Cell(0, 0, 'Tlp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);

            PDF::Cell(40, 0, 'Periode (harian):', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' '.$start_date.' s/d '.$end_date, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Tanggal Cetak:', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' ' . date('d-m-Y h:m'), 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);

            PDF::Cell(40, 0, 'Nama:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->nama, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Bagian:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->bagian, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Upah', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($nilai_upah, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Uang Makan', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($uang_makan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);

            PDF::Cell(40, 0, 'Lbr. Rutin', 0, 0, 'L', 0, '', 0);
            PDF::Cell(0, 0, ' '.number_format($lembur_rutin, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lbr. Biasa', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_biasa, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);

            PDF::Cell(40, 0, 'Potongan lain-lain', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total_pot_absensi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Potongan BPJS', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_bpjs, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);

            PDF::SetFont('times', 'B', 9);
            PDF::Cell(40, 0, 'Total', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(10);
            PDF::SetFont('times', '', 9);

            PDF::Cell(0, 0, 'Penerima', 0, 0, 'L', 0, '', 0);
            PDF::Ln(12);
            PDF::Cell(0, 0, '(               )', 0, 0, 'L', 0, '', 0);
            PDF::Ln(17);
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('slip_gaji.pdf');

        // need to call exit, i don't know why
        exit;
    }

    private function slipGajiKaryawanTetap__($tanggal, $hingga, $potongan)
    {
        $start_date = DateTime::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
        ;
        $end_date = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
        $potongan = $potongan;

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Print Slip Gaji - Trimitra Kemasindo');
        PDF::SetSubject('Slip Gaji Karyawan');
        PDF::SetKeywords('Slip Gaji Karyawan Trimitra Kemasindo');

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        $format = array(216, 356); // legal paper size
        PDF::AddPage('P', $format);
        PDF::SetMargins(15, 10);
        PDF::setX(15);

        $karyawans = Karyawan::select('nik')->where('status_karyawan_id', 1)->orderBy('nama')->get();
        for ($i = 0; $i < sizeof($karyawans); $i++) {
            if ($i > 0 && $i % SLIP_PER_PAGE == 0) {
                PDF::AddPage('P', $format);
                PDF::setX(15);
            }

            $nik = $karyawans[$i]->nik;

            $karyawan = DB::table('karyawans')->where('karyawans.nik', '=', $nik)->first();
            $gaji = $karyawan->nilai_upah;
            if ($potongan == 'bpjs') {
                $pot_bpjs = $karyawan->pot_bpjs;
            } else {
                $pot_bpjs = 0;
            }

            //HITUNG TOTAL JAM KERJA
            $total_jam = DB::table('absensi_harians')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->sum('jam');

            //HITUNG JUMLAH ABSEN SELURUHNYA (include izin / ga masuk)
            $starts = new Carbon($start_date);
            $ends = new Carbon($end_date);

            $jml_absen = DB::select(DB::raw("SELECT COUNT(id) counter
                            FROM `absensi_harians` where `karyawan_id` = :nik AND `status` = 2 AND (`jam_kerja` !='LEMBUR' OR `jam_kerja` IS NULL) AND `tanggal` between :starts AND :ends
                            "), ['nik' => $nik, 'starts' => $starts, 'ends' => $ends]);

            //HITUNG JUMLAH MASUK KERJA (tidak termasuk izin / ga absen)
            $hari_kerja = DB::table('absensi_harians')
                            ->where('jml_kehadiran', '!=', '00:00:00')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->count('jml_kehadiran');

            //HITUNG JUMLAH TIDAK MASUK KERJA
            $hari_off = DB::table('absensi_harians')
                            ->whereNull('jml_kehadiran')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->count('id');

            //      KARYAWAN BULANAN
            //$nilai_upah = $jml_absen[0]->counter * ($gaji + $karyawan->uang_makan);
            $nilai_upah = $gaji;
            // HITUNG UANG MAKAN
            $uang_makan = $hari_kerja * $karyawan->uang_makan;

            // PERHITUNGAN LEMBUR
            $total_lembur_rutin = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 1)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_rutin = 14200 * $total_lembur_rutin;

            $total_lembur_biasa = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_biasa = 21300 * $total_lembur_biasa;

            $total_lembur_off = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 3)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_off = 28400 * $total_lembur_off;

            //CEK DIA PUNYA TUNJANGAN GA
            if ($karyawan->tunjangan !=0) {
            // PERHITUNGAN POTONGAN JABATAN
                $pot_jabatan = (0.25 * $karyawan->tunjangan) * $hari_off;
                $pot_umk = 0;
            } else {
                // PERHITUNGAN POTONGAN UMK
                $pot_umk = 50000 * $hari_off;
                $pot_jabatan = 0;
            }

            // PENJUMLAHAN POTONGAN ABSENSI
            $total_pot_absensi = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('pot_absensi');

            // PERHITUNGAN TOTAL
            $total = ($nilai_upah + $karyawan->tunjangan + $lembur_rutin + $uang_makan + $lembur_biasa + $lembur_off) - ($pot_jabatan + $pot_umk + $total_pot_absensi + $pot_bpjs + $karyawan->pot_koperasi);
            //$total = 0;

            PDF::SetFont('times', 'B', 10);

            PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 9);
            PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15, Bandung', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::Cell(0, 0, 'Tlp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);
            PDF::Ln(6);

            PDF::Cell(40, 0, 'Nama:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->nama, 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Periode (bulanan):', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' '.$start_date.' s/d '.$end_date, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Bagian:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->bagian, 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Tanggal Cetak:', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' ' . date('d-m-Y h:m'), 0, 0, 'L', 0, '', 0);
            PDF::Ln(5);

            PDF::Cell(40, 0, 'Upah:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($nilai_upah, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan Jab.:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_jabatan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Tunj. Jab.:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($karyawan->tunjangan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan Umk:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_umk, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Uang Makan:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($uang_makan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan Lain Lain:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total_pot_absensi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lbr. Rutin:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_rutin, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan BPJS:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_bpjs, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lbr. Biasa:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_biasa, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan Koperasi:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($karyawan->pot_koperasi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(5);

            PDF::Cell(40, 0, 'Lembur Off:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_off, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::SetFont('times', 'B', 9);
            PDF::Cell(40, 0, 'Total:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(6);
            PDF::SetFont('times', '', 9);

            PDF::Cell(0, 0, 'Penerima', 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);
            PDF::Cell(0, 0, '(               )', 0, 0, 'L', 0, '', 0);
            PDF::Ln(10);
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('slip_gaji.pdf');

        // need to call exit, i don't know why
        exit;
    }

    private function slipGajiKaryawanHarian__($tanggal, $hingga, $potongan)
    {
        $start_date = DateTime::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
        ;
        $end_date = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
        $potongan = $potongan;

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Print Slip Gaji - Trimitra Kemasindo');
        PDF::SetSubject('Slip Gaji Karyawan');
        PDF::SetKeywords('Slip Gaji Karyawan Trimitra Kemasindo');

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        $format = array(216, 356); // legal paper size
        PDF::AddPage('P', $format);
        PDF::SetMargins(15, 10);
        PDF::setX(15);

        $karyawans = Karyawan::select('nik')->where('status_karyawan_id', 2)->orderBy('nama')->get();
        for ($i = 0; $i < sizeof($karyawans); $i++) {
            if ($i > 0 && $i % SLIP_PER_PAGE_ALT == 0) {
                PDF::AddPage('P', $format);
                PDF::setX(15);
            }

            $nik = $karyawans[$i]->nik;

            $karyawan = DB::table('karyawans')->where('karyawans.nik', '=', $nik)->first();
            $gaji = $karyawan->nilai_upah;
            if ($potongan == 'bpjs') {
                $pot_bpjs = $karyawan->pot_bpjs;
            } else {
                $pot_bpjs = 0;
            }

            //HITUNG JUMLAH KERJA
            $hari_kerja = DB::table('absensi_harians')
                            ->where('jml_kehadiran', '!=', '00:00:00')
                            ->where('absensi_harians.karyawan_id', '=', $nik)
                            ->where('absensi_harians.status', '=', 2)
                            ->whereNotNull('absensi_harians.jml_jam_kerja')
                            ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                            ->count('jml_kehadiran');

            // HITUNG UANG MAKAN
            $uang_makan = $hari_kerja * $karyawan->uang_makan;

            // PERHITUNGAN LEMBUR
            $total_lembur_rutin = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 1)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_rutin = $total_lembur_rutin * 11700;

            $total_lembur_biasa = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.jenis_lembur', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('konfirmasi_lembur');

            $lembur_biasa = $total_lembur_biasa * 17600;

            // PENJUMLAHAN POTONGAN ABSENSI
            $total_pot_absensi = DB::table('absensi_harians')
                                    ->where('absensi_harians.karyawan_id', '=', $nik)
                                    ->where('absensi_harians.status', '=', 2)
                                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->sum('pot_absensi');

            $total_upah_harian = DB::table('absensi_harians')
                                    ->select('absensi_harians.pot_absensi')
                                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                                    ->whereBetween('absensi_harians.tanggal', [new Carbon($start_date), new Carbon($end_date)])
                                    ->where('absensi_harians.status', '=', 2)
                                    ->where('karyawans.nik', '=', $nik)
                                    ->sum('absensi_harians.upah_harian');

            // dd($total_upah_harian);
            $nilai_upah = $total_upah_harian + $total_pot_absensi - ($uang_makan + $lembur_rutin + $lembur_biasa);

            // PERHITUNGAN TOTAL
            $total = $nilai_upah + $uang_makan + $lembur_rutin + $lembur_biasa - ($pot_bpjs + $total_pot_absensi);

            PDF::SetFont('times', 'B', 10);

            PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 9);
            PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15, Bandung', 0, 0, 'L', 0, '', 0);
            PDF::Ln();
            PDF::Cell(0, 0, 'Tlp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);
            PDF::Ln(6);

            PDF::Cell(40, 0, 'Nama:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->nama, 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Periode (harian):', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' '.$start_date.' s/d '.$end_date, 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Bagian:', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.$karyawan->bagian, 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Tanggal Cetak:', 0, 'L', false, 0);
            PDF::Cell(40, 0, ' ' . date('d-m-Y h:m'), 0, 0, 'L', 0, '', 0);
            PDF::Ln(5);

            PDF::Cell(40, 0, 'Upah', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($nilai_upah, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan lain-lain', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total_pot_absensi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Uang Makan', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($uang_makan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, 'Potongan BPJS', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($pot_bpjs, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lbr. Rutin', 0, 0, 'L', 0, '', 0);
            PDF::Cell(0, 0, ' '.number_format($lembur_rutin, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln();

            PDF::Cell(40, 0, 'Lbr. Biasa', 0, 0, 'L', 0, '', 0);
            PDF::Cell(60, 0, ' '.number_format($lembur_biasa, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::SetFont('times', 'B', 9);
            PDF::Cell(40, 0, 'Total', 0, 0, 'L', 0, '', 0);
            PDF::Cell(40, 0, ' '.number_format($total, 0, '.', ','), 0, 0, 'L', 0, '', 0);
            PDF::Ln(6);
            PDF::SetFont('times', '', 9);

            PDF::Cell(0, 0, 'Penerima', 0, 0, 'L', 0, '', 0);
            PDF::Ln(8);
            PDF::Cell(0, 0, '(               )', 0, 0, 'L', 0, '', 0);
            PDF::Ln(10);
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('slip_gaji.pdf');

        // need to call exit, i don't know why
        exit;
    }

    // prompt
    public function penerimaanPembayaranAngkutan()
    {
        $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
        $data['default_date'] = date('d-m-Y');
        $data['default_year'] = date('Y');

        return view('report.penerimaan_pembayaran_params', $data);
    }

    // preview
    public function previewPenerimaanPembayaranAngkutan($angkutan, $tanggal, $hingga)
    {
        //$period = $tahun.$bulan;

        $tanggal_en = DateTime::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');

        if ($angkutan !== '0') {
            $hingga_en = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
            //DB::connection()->enableQueryLog();

            $data = DB::select(DB::raw("SELECT `invoice_penjualans`.`id`, `invoice_penjualans`.`tanggal`, `invoice_penjualans`.`no_surat_jalan`, `angkutans`.`nama` as `nama_angkutan`,
`invoice_penjualans`.`no_mobil`, `tujuans`.`kota` as `nama_tujuan`, `invoice_penjualans`.`harga_angkutan`, `invoice_penjualans`.`diskon_bayar_angkutan`,
`invoice_penjualans`.`jumlah_bayar_angkutan`, `invoice_penjualans`.`status_bayar_angkutan`, `invoice_penjualans`.`tanggal_bayar_angkutan`, `invoice_penjualans`.`keterangan_bayar_angkutan`
from `invoice_penjualans` inner join `angkutans` on `angkutans`.`id` = `invoice_penjualans`.`angkutan_id`
inner join `tujuans` on `tujuans`.`id` = `invoice_penjualans`.`tujuan_id`
where `invoice_penjualans`.`tanggal` between :starts and :ends and `angkutans`.`id` = :angkutan
and (`invoice_penjualans`.`status_bayar_angkutan` != 2 or `invoice_penjualans`.`status_bayar_angkutan` is null) order by `invoice_penjualans`.`tanggal` asc, `invoice_penjualans`.`no_surat_jalan` asc"), ['starts'=> $tanggal_en, 'ends' => $hingga_en, 'angkutan' => $angkutan]);

            $data_unpaid = DB::select(DB::raw("SELECT `invoice_penjualans`.`id`, `invoice_penjualans`.`tanggal`, `invoice_penjualans`.`no_surat_jalan`, `angkutans`.`nama` as `nama_angkutan`,
`invoice_penjualans`.`no_mobil`, `tujuans`.`kota` as `nama_tujuan`, `invoice_penjualans`.`harga_angkutan`, `invoice_penjualans`.`diskon_bayar_angkutan`,
`invoice_penjualans`.`jumlah_bayar_angkutan`, `invoice_penjualans`.`status_bayar_angkutan`, `invoice_penjualans`.`tanggal_bayar_angkutan`, `invoice_penjualans`.`keterangan_bayar_angkutan`
from `invoice_penjualans` inner join `angkutans` on `angkutans`.`id` = `invoice_penjualans`.`angkutan_id`
inner join `tujuans` on `tujuans`.`id` = `invoice_penjualans`.`tujuan_id`
where `invoice_penjualans`.`tanggal` between :starts and :ends and `angkutans`.`id` = :angkutan
and (`invoice_penjualans`.`status_bayar_angkutan` != 1 and `invoice_penjualans`.`status_bayar_angkutan` != 2 or `invoice_penjualans`.`status_bayar_angkutan` is null) order by `invoice_penjualans`.`tanggal` asc, `invoice_penjualans`.`no_surat_jalan` asc"), ['starts'=> $tanggal_en, 'ends' => $hingga_en, 'angkutan' => $angkutan]);
        } else {
            $hingga_en = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');

            $data = DB::select(DB::raw("SELECT `invoice_penjualans`.`id`, `invoice_penjualans`.`tanggal`, `invoice_penjualans`.`no_surat_jalan`, `angkutans`.`nama` as `nama_angkutan`,
`invoice_penjualans`.`no_mobil`, `tujuans`.`kota` as `nama_tujuan`, `invoice_penjualans`.`harga_angkutan`, `invoice_penjualans`.`diskon_bayar_angkutan`,
`invoice_penjualans`.`jumlah_bayar_angkutan`, `invoice_penjualans`.`status_bayar_angkutan`, `invoice_penjualans`.`tanggal_bayar_angkutan`, `invoice_penjualans`.`keterangan_bayar_angkutan`
from `invoice_penjualans` inner join `angkutans` on `angkutans`.`id` = `invoice_penjualans`.`angkutan_id`
inner join `tujuans` on `tujuans`.`id` = `invoice_penjualans`.`tujuan_id`
where `invoice_penjualans`.`tanggal` between :starts and :ends
and (`invoice_penjualans`.`status_bayar_angkutan` != 2 or `invoice_penjualans`.`status_bayar_angkutan` is null) order by `invoice_penjualans`.`tanggal` asc, `invoice_penjualans`.`no_surat_jalan` asc"), ['starts'=> $tanggal_en, 'ends' => $hingga_en]);

            $data_unpaid = DB::select(DB::raw("SELECT `invoice_penjualans`.`id`, `invoice_penjualans`.`tanggal`, `invoice_penjualans`.`no_surat_jalan`, `angkutans`.`nama` as `nama_angkutan`,
`invoice_penjualans`.`no_mobil`, `tujuans`.`kota` as `nama_tujuan`, `invoice_penjualans`.`harga_angkutan`, `invoice_penjualans`.`diskon_bayar_angkutan`,
`invoice_penjualans`.`jumlah_bayar_angkutan`, `invoice_penjualans`.`status_bayar_angkutan`, `invoice_penjualans`.`tanggal_bayar_angkutan`, `invoice_penjualans`.`keterangan_bayar_angkutan`
from `invoice_penjualans` inner join `angkutans` on `angkutans`.`id` = `invoice_penjualans`.`angkutan_id`
inner join `tujuans` on `tujuans`.`id` = `invoice_penjualans`.`tujuan_id`
where `invoice_penjualans`.`tanggal` between :starts and :ends
and (`invoice_penjualans`.`status_bayar_angkutan` != 1 and `invoice_penjualans`.`status_bayar_angkutan` != 2 or `invoice_penjualans`.`status_bayar_angkutan` is null) order by `invoice_penjualans`.`tanggal` asc, `invoice_penjualans`.`no_surat_jalan` asc"), ['starts'=> $tanggal_en, 'ends' => $hingga_en]);
        }

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Penerimaan Pembayaran - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Penerimaan Pembayaran');
        PDF::SetKeywords('Laporan Penerimaan Pembayaran Trimitra Kemasindo');

        PDF::setFooterCallback(function ($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
        });

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('L', 'A3');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10, 15);

        // Image ($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array())
        //PDF::Image(asset('/image/bmt.png'), 15, 5, 35, 15, '', '', 'T', true);

        // SetFont ($family, $style='', $size=null, $fontfile='', $subset='default', $out=true)
        PDF::SetFont('times', 'B', 12);

        //PDF::setXY(54, 10);
        PDF::setX(15);
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        //PDF::setXY(54, 16);
        PDF::setX(15);
        PDF::SetFont('', '', 10);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15 Bandung, Telp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);

         // Line ($x1, $y1, $x2, $y2, $style=array())
        $style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        PDF::Line(15, 21, 400, 21); // $y2 = 282 for A4
        PDF::Line(15, 22, 400, 22, $style); // $y2 = 402 for A3
        PDF::setLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        PDF::Ln(12);

        PDF::SetFont('', 'B', 12);

        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        PDF::Cell(0, 0, 'LAPORAN PEMBAYARAN ANGKUTAN', 0, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::SetFont('', '', 10);

        PDF::Cell(30, 0, 'TANGGAL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'HINGGA', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        PDF::Cell(30, 0, $tanggal, 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, $hingga, 1, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::Cell(25, 0, 'TANGGAL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(35, 0, 'NO. SURAT JALAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(25, 0, 'ANGKUTAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'NO. MOBIL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'TUJUAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(20, 0, 'BIAYA', 1, 0, 'C', 0, '', 0);
        PDF::Cell(20, 0, 'POTONGAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(20, 0, 'JUMLAH', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'STATUS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(20, 0, 'TGL. BYR', 1, 0, 'C', 0, '', 0);
        PDF::Cell(125, 0, 'KETERANGAN', 1, 0, 'C', 0, '', 0);
        PDF::Ln();

        $count = sizeof($data);
        if ($count > 0) {
            $grandTotal = 0;
            $grandTotalUnpaid = 0;
            foreach ($data as $item) {
                PDF::Cell(25, 0, Carbon::createFromFormat('Y-m-d', $item->tanggal)->format('d-m-Y'), 1, 0, 'L', 0, '', 1);
                PDF::Cell(35, 0, $item->no_surat_jalan, 1, 0, 'L', 0, '', 1);
                PDF::Cell(25, 0, $item->nama_angkutan, 1, 0, 'L', 0, '', 1);
                PDF::Cell(30, 0, $item->no_mobil, 1, 0, 'L', 0, '', 1);
                PDF::Cell(40, 0, $item->nama_tujuan, 1, 0, 'L', 0, '', 1);
                PDF::Cell(20, 0, number_format($item->harga_angkutan, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(20, 0, number_format($item->diskon_bayar_angkutan, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(20, 0, number_format($item->jumlah_bayar_angkutan, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(24, 0, ($item->status_bayar_angkutan == 1 ? 'Sudah Bayar' : 'Belum Bayar'), 1, 0, 'L', 0, '', 1);
                PDF::Cell(20, 0, ($item->tanggal_bayar_angkutan ? Carbon::createFromFormat('Y-m-d', $item->tanggal_bayar_angkutan)->format('d-m-Y') : ''), 1, 0, 'C', 0, '', 1);

                PDF::Cell(125, 0, $item->keterangan_bayar_angkutan, 1, 0, 'J', 0, '', 1);
                PDF::Ln();

                $grandTotal += $item->jumlah_bayar_angkutan;
            }

            foreach ($data_unpaid as $item_unpaid) {
                $grandTotalUnpaid += $item_unpaid->harga_angkutan;
            }
            PDF::SetFont('', 'B', 10);
            // grand total
            PDF::Cell(115, 0, 'TOTAL ', 1, 0, 'R', 0, '', 1);
            PDF::Cell(60, 0, number_format($grandTotal, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(209, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
            PDF::Cell(115, 0, 'TOTAL BELUM BAYAR', 1, 0, 'R', 0, '', 1);
            PDF::Cell(60, 0, number_format($grandTotalUnpaid, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(209, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 10);
        } else {
            PDF::Cell(25, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(35, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(25, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(20, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(20, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(20, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(125, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
        }

            // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
            PDF::Output('laporan_pembayaran_angkutan.pdf');

            // need to call exit, i don't know why
            exit;
    }

    public function penjualan()
    {
        $data['default_date'] = date('d-m-Y');

        return view('report.penjualan_params', $data);
    }

    // preview
    public function previewPenjualan($ppn, $tanggal, $hingga = '')
    {
        //$tanggal_en = Carbon::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
        $tanggal_en = DateTime::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');

        if ($hingga !== '') {
            //$hingga_en = Carbon::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
            $hingga_en = DateTime::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');

            if ($ppn == 'Y') {
                $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select(
                        'invoice_penjualans.id',
                        'invoice_penjualans.tanggal',
                        'invoice_penjualans.no_invoice',
                        'invoice_penjualans.no_po',
                        'invoice_penjualans.no_surat_jalan',
                        'invoice_penjualans.no_mobil',
                        'invoice_penjualans.tgl_jatuh_tempo',
                        'invoice_penjualans.bank_tujuan_bayar',
                        'invoice_penjualans.tanggal_bayar',
                        'invoice_penjualans.status_bayar',
                        'invoice_penjualans.keterangan',
                        'invoice_penjualans.sub_total',
                        'invoice_penjualans.diskon',
                        'invoice_penjualans.total',
                        'invoice_penjualans.ppn',
                        'invoice_penjualans.grand_total',
                        'detail_penjualans.jumlah_ball',
                        'detail_penjualans.jumlah as jumlah_pcs',
                        'detail_penjualans.harga_barang',
                        'detail_penjualans.subtotal',
                        'angkutans.nama as nama_angkutan',
                        'tujuans.kota as nama_tujuan',
                        'konsumens.nama as nama_konsumen',
                        'barangs.jenis as jenis_barang',
                        'barangs.nama as nama_barang',
                        'barangs.berat as berat_barang'
                    )
                    ->whereBetween('invoice_penjualans.tanggal', [$tanggal_en, $hingga_en])
                    ->where('invoice_penjualans.ppn', '>', 0)
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
            } elseif ($ppn == 'N') {
                $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select(
                        'invoice_penjualans.id',
                        'invoice_penjualans.tanggal',
                        'invoice_penjualans.no_invoice',
                        'invoice_penjualans.no_po',
                        'invoice_penjualans.no_surat_jalan',
                        'invoice_penjualans.no_mobil',
                        'invoice_penjualans.tgl_jatuh_tempo',
                        'invoice_penjualans.bank_tujuan_bayar',
                        'invoice_penjualans.tanggal_bayar',
                        'invoice_penjualans.status_bayar',
                        'invoice_penjualans.keterangan',
                        'invoice_penjualans.sub_total',
                        'invoice_penjualans.diskon',
                        'invoice_penjualans.total',
                        'invoice_penjualans.ppn',
                        'invoice_penjualans.grand_total',
                        'detail_penjualans.jumlah_ball',
                        'detail_penjualans.jumlah as jumlah_pcs',
                        'detail_penjualans.harga_barang',
                        'detail_penjualans.subtotal',
                        'angkutans.nama as nama_angkutan',
                        'tujuans.kota as nama_tujuan',
                        'konsumens.nama as nama_konsumen',
                        'barangs.jenis as jenis_barang',
                        'barangs.nama as nama_barang',
                        'barangs.berat as berat_barang'
                    )
                    ->whereBetween('invoice_penjualans.tanggal', [$tanggal_en, $hingga_en])
                    ->where('invoice_penjualans.ppn', '=', 0)
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
            } else {
                $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select(
                        'invoice_penjualans.id',
                        'invoice_penjualans.tanggal',
                        'invoice_penjualans.no_invoice',
                        'invoice_penjualans.no_po',
                        'invoice_penjualans.no_surat_jalan',
                        'invoice_penjualans.no_mobil',
                        'invoice_penjualans.tgl_jatuh_tempo',
                        'invoice_penjualans.bank_tujuan_bayar',
                        'invoice_penjualans.tanggal_bayar',
                        'invoice_penjualans.status_bayar',
                        'invoice_penjualans.keterangan',
                        'invoice_penjualans.sub_total',
                        'invoice_penjualans.diskon',
                        'invoice_penjualans.total',
                        'invoice_penjualans.ppn',
                        'invoice_penjualans.grand_total',
                        'detail_penjualans.jumlah_ball',
                        'detail_penjualans.jumlah as jumlah_pcs',
                        'detail_penjualans.harga_barang',
                        'detail_penjualans.subtotal',
                        'angkutans.nama as nama_angkutan',
                        'tujuans.kota as nama_tujuan',
                        'konsumens.nama as nama_konsumen',
                        'barangs.jenis as jenis_barang',
                        'barangs.nama as nama_barang',
                        'barangs.berat as berat_barang'
                    )
                    ->whereBetween('invoice_penjualans.tanggal', [$tanggal_en, $hingga_en])
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
            }
        } else {
            if ($ppn == 'Y') {
                $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select(
                        'invoice_penjualans.id',
                        'invoice_penjualans.tanggal',
                        'invoice_penjualans.no_invoice',
                        'invoice_penjualans.no_po',
                        'invoice_penjualans.no_surat_jalan',
                        'invoice_penjualans.no_mobil',
                        'invoice_penjualans.tgl_jatuh_tempo',
                        'invoice_penjualans.bank_tujuan_bayar',
                        'invoice_penjualans.tanggal_bayar',
                        'invoice_penjualans.status_bayar',
                        'invoice_penjualans.keterangan',
                        'invoice_penjualans.sub_total',
                        'invoice_penjualans.diskon',
                        'invoice_penjualans.total',
                        'invoice_penjualans.ppn',
                        'invoice_penjualans.grand_total',
                        'detail_penjualans.jumlah_ball',
                        'detail_penjualans.jumlah as jumlah_pcs',
                        'detail_penjualans.harga_barang',
                        'detail_penjualans.subtotal',
                        'angkutans.nama as nama_angkutan',
                        'tujuans.kota as nama_tujuan',
                        'konsumens.nama as nama_konsumen',
                        'barangs.jenis as jenis_barang',
                        'barangs.nama as nama_barang',
                        'barangs.berat as berat_barang'
                    )
                    ->where('invoice_penjualans.tanggal', $tanggal_en)
                    ->where('invoice_penjualans.ppn', '>', 0)
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
            } elseif ($ppn == 'N') {
                $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select(
                        'invoice_penjualans.id',
                        'invoice_penjualans.tanggal',
                        'invoice_penjualans.no_invoice',
                        'invoice_penjualans.no_po',
                        'invoice_penjualans.no_surat_jalan',
                        'invoice_penjualans.no_mobil',
                        'invoice_penjualans.tgl_jatuh_tempo',
                        'invoice_penjualans.bank_tujuan_bayar',
                        'invoice_penjualans.tanggal_bayar',
                        'invoice_penjualans.status_bayar',
                        'invoice_penjualans.keterangan',
                        'invoice_penjualans.sub_total',
                        'invoice_penjualans.diskon',
                        'invoice_penjualans.total',
                        'invoice_penjualans.ppn',
                        'invoice_penjualans.grand_total',
                        'detail_penjualans.jumlah_ball',
                        'detail_penjualans.jumlah as jumlah_pcs',
                        'detail_penjualans.harga_barang',
                        'detail_penjualans.subtotal',
                        'angkutans.nama as nama_angkutan',
                        'tujuans.kota as nama_tujuan',
                        'konsumens.nama as nama_konsumen',
                        'barangs.jenis as jenis_barang',
                        'barangs.nama as nama_barang',
                        'barangs.berat as berat_barang'
                    )
                    ->where('invoice_penjualans.tanggal', $tanggal_en)
                    ->where('invoice_penjualans.ppn', '=', 0)
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
            } else {
                $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select(
                        'invoice_penjualans.id',
                        'invoice_penjualans.tanggal',
                        'invoice_penjualans.no_invoice',
                        'invoice_penjualans.no_po',
                        'invoice_penjualans.no_surat_jalan',
                        'invoice_penjualans.no_mobil',
                        'invoice_penjualans.tgl_jatuh_tempo',
                        'invoice_penjualans.bank_tujuan_bayar',
                        'invoice_penjualans.tanggal_bayar',
                        'invoice_penjualans.status_bayar',
                        'invoice_penjualans.keterangan',
                        'invoice_penjualans.sub_total',
                        'invoice_penjualans.diskon',
                        'invoice_penjualans.total',
                        'invoice_penjualans.ppn',
                        'invoice_penjualans.grand_total',
                        'detail_penjualans.jumlah_ball',
                        'detail_penjualans.jumlah as jumlah_pcs',
                        'detail_penjualans.harga_barang',
                        'detail_penjualans.subtotal',
                        'angkutans.nama as nama_angkutan',
                        'tujuans.kota as nama_tujuan',
                        'konsumens.nama as nama_konsumen',
                        'barangs.jenis as jenis_barang',
                        'barangs.nama as nama_barang',
                        'barangs.berat as berat_barang'
                    )
                    ->where('invoice_penjualans.tanggal', $tanggal_en)
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
            }
        }

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Penjualan - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Penjualan');
        PDF::SetKeywords('Laporan Penjualan Trimitra Kemasindo');

        PDF::setFooterCallback(function ($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
        });

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('L', 'A2');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10, 15);

        // Image ($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array())
        //PDF::Image(asset('/image/bmt.png'), 15, 5, 35, 15, '', '', 'T', true);

        // SetFont ($family, $style='', $size=null, $fontfile='', $subset='default', $out=true)
        PDF::SetFont('times', 'B', 12);

        //PDF::setXY(54, 10);
        PDF::setX(15);
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        //PDF::setXY(54, 16);
        PDF::setX(15);
        PDF::SetFont('', '', 10);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15 Bandung, Telp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);

         // Line ($x1, $y1, $x2, $y2, $style=array())
        $style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        PDF::Line(15, 21, 576, 21); // $y2 = 282 for A4
        PDF::Line(15, 22, 576, 22, $style); // $y2 = 402 for A3
        PDF::setLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        PDF::Ln(12);

        PDF::SetFont('', 'B', 12);

        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        PDF::Cell(0, 0, 'LAPORAN PENJUALAN', 0, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::SetFont('', '', 10);

        PDF::Cell(30, 0, 'DARI', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'HINGGA', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        PDF::Cell(30, 0, $tanggal, 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, $hingga, 1, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::Cell(22, 0, 'TANGGAL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(26, 0, 'NO. FAKTUR', 1, 0, 'C', 0, '', 0);
        PDF::Cell(50, 0, 'DISTRIBUTOR', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'KOTA', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'JENIS BARANG', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'NAMA BARANG', 1, 0, 'C', 0, '', 0);
        PDF::Cell(12, 0, 'BALL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(12, 0, 'PCS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(20, 0, 'BERAT', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'HARGA / PCS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'JUMLAH', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'SUBTOTAL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'DISC', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'TOTAL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'PPN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'GRAND TOTAL', 1, 0, 'C', 0, '', 0);
        PDF::Cell(26, 0, 'JTH. TEMPO', 1, 0, 'C', 0, '', 0);
        PDF::Cell(22, 0, 'BANK', 1, 0, 'C', 0, '', 0);
        PDF::Cell(26, 0, 'TGL. BAYAR', 1, 0, 'C', 0, '', 0);
        PDF::Cell(24, 0, 'STATUS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(36, 0, 'KETERANGAN', 1, 0, 'C', 0, '', 0);
        PDF::Ln();

        $count = sizeof($data);
        if ($count > 0) {
            $checkInvoice = '';
            $grandTotal = 0;
            foreach ($data as $item) {
                if ($checkInvoice != $item->no_invoice) {
                    PDF::Cell(22, 0, Carbon::createFromFormat('Y-m-d', $item->tanggal)->format('d-m-Y'), 1, 0, 'L', 0, '', 1);
                    PDF::Cell(26, 0, $item->no_invoice, 1, 0, 'L', 0, '', 1);
                    PDF::Cell(50, 0, $item->nama_konsumen, 1, 0, 'L', 0, '', 1);
                    PDF::Cell(30, 0, $item->nama_tujuan, 1, 0, 'L', 0, '', 1);
                } else {
                    PDF::Cell(22, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(26, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(50, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(30, 0, '', 1, 0, 'L', 0, '', 1);
                }

                PDF::Cell(40, 0, $item->jenis_barang, 1, 0, 'L', 0, '', 1);
                PDF::Cell(40, 0, $item->nama_barang, 1, 0, 'L', 0, '', 1);
                PDF::Cell(12, 0, number_format($item->jumlah_ball, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(12, 0, number_format($item->jumlah_pcs, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(20, 0, number_format($item->berat_barang, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(24, 0, number_format($item->harga_barang, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(24, 0, number_format($item->subtotal, 2, '.', ','), 1, 0, 'R', 0, '', 1);

                if ($checkInvoice != $item->no_invoice) {
                    PDF::Cell(24, 0, number_format($item->sub_total, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                    PDF::Cell(24, 0, number_format($item->diskon, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                    PDF::Cell(24, 0, number_format($item->total, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                    PDF::Cell(24, 0, number_format($item->ppn, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                    PDF::Cell(30, 0, number_format($item->grand_total, 2, '.', ','), 1, 0, 'R', 0, '', 1);
                    PDF::Cell(26, 0, Carbon::createFromFormat('Y-m-d', $item->tgl_jatuh_tempo)->format('d-m-Y'), 1, 0, 'L', 0, '', 1);
                    PDF::Cell(22, 0, $item->bank_tujuan_bayar, 1, 0, 'L', 0, '', 1);
                    PDF::Cell(26, 0, ($item->status_bayar == 1 ? Carbon::createFromFormat('Y-m-d', $item->tanggal_bayar)->format('d-m-Y') : ''), 1, 0, 'L', 0, '', 1);
                    PDF::Cell(24, 0, ($item->status_bayar == 1 ? 'Sudah Bayar' : 'Belum Bayar'), 1, 0, 'L', 0, '', 1);
                    PDF::Cell(36, 0, $item->keterangan, 1, 0, 'L', 0, '', 1);

                    $grandTotal += $item->grand_total;
                } else {
                    PDF::Cell(24, 0, '', 1, 0, 'R', 0, '', 1);
                    PDF::Cell(24, 0, '', 1, 0, 'R', 0, '', 1);
                    PDF::Cell(24, 0, '', 1, 0, 'R', 0, '', 1);
                    PDF::Cell(24, 0, '', 1, 0, 'R', 0, '', 1);
                    PDF::Cell(30, 0, '', 1, 0, 'R', 0, '', 1);
                    PDF::Cell(26, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(22, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(26, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(24, 0, '', 1, 0, 'L', 0, '', 1);
                    PDF::Cell(36, 0, '', 1, 0, 'L', 0, '', 1);
                }
                PDF::Ln();

                $checkInvoice = $item->no_invoice;
            }
            PDF::SetFont('', 'B', 10);
            // grand total
            PDF::Cell(396, 0, 'TOTAL ', 1, 0, 'R', 0, '', 1);
            PDF::Cell(30, 0, number_format($grandTotal, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(134, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 10);
        } else {
            PDF::Cell(22, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(26, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(50, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(12, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(12, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(20, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(26, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(22, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(26, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(36, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('laporan_penjualan.pdf');

        // need to call exit, i don't know why
        exit;
    }

    public function absensiKaryawanTetap()
    {
        $data['default_date'] = date('d-m-Y');

        return view('report.absensi_karyawan_tetap_params', $data);
    }

    // preview
    public function previewAbsensiKaryawanTetap($tanggal_awal, $tanggal_akhir, $potongan)
    {
        // $tahun_skrg = date('Y');

        $tgl_awal = Carbon::createFromFormat('d-m-Y', $tanggal_awal)->format('Y-m-d');
        $tgl_akhir = Carbon::createFromFormat('d-m-Y', $tanggal_akhir)->format('Y-m-d');


        //DB::connection()->enableQueryLog();
        $data = AbsensiHarian::select('absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.nik', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi', 'karyawans.nik', 'karyawans.nama', 'karyawans.norek', 'karyawans.uang_makan', 'karyawans.nilai_upah', 'karyawans.pot_koperasi', 'karyawans.tgl_masuk', 'karyawans.tunjangan', 'karyawans.pot_bpjs')
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
        ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
        ->where('absensi_harians.status', '=', 2)
        ->where('karyawans.status_karyawan_id', '=', 1)
        //->where('karyawans.nik', '=', 267)
        ->groupBy('karyawans.nik')
        ->get();
         // $queries = DB::getQueryLog();

         // dd($queries);

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Absensi Karyawan Tetap / Kontrak - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Absensi Karyawan Tetap / Kontrak');
        PDF::SetKeywords('Laporan Penjualan Trimitra Kemasindo');

        PDF::setFooterCallback(function ($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
        });

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('L', 'A2');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10, 15);

        // Image ($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array())
        //PDF::Image(asset('/image/bmt.png'), 15, 5, 35, 15, '', '', 'T', true);

        // SetFont ($family, $style='', $size=null, $fontfile='', $subset='default', $out=true)
        PDF::SetFont('times', 'B', 12);

        //PDF::setXY(54, 10);
        PDF::setX(15);
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        //PDF::setXY(54, 16);
        PDF::setX(15);
        PDF::SetFont('', '', 10);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15 Bandung, Telp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);

         // Line ($x1, $y1, $x2, $y2, $style=array())
        $style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        PDF::Line(15, 21, 576, 21); // $y2 = 282 for A4
        PDF::Line(15, 22, 576, 22, $style); // $y2 = 402 for A3
        PDF::setLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        PDF::Ln(12);

        PDF::SetFont('', 'B', 12);

        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        PDF::Cell(0, 0, 'LAPORAN ABSENSI PEGAWAI BULANAN', 0, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::SetFont('', '', 10);

        PDF::Cell(90, 0, 'PERIODE', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        PDF::Cell(90, 0, $tanggal_awal.' hingga '.$tanggal_akhir, 1, 0, 'C', 0, '', 0);

        PDF::Ln(8);

        PDF::Cell(40, 0, 'NAMA KARYAWAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'TGL. MASUK KERJA', 1, 0, 'C', 0, '', 0);
        PDF::Cell(50, 0, 'NO. REKENING', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'TOTAL UPAH', 1, 0, 'C', 0, '', 0);
        PDF::Cell(45, 0, 'POTONGAN KOPERASI', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'POTONGAN LAIN LAIN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'POTONGAN BPJS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(35, 0, 'SETELAH DI POT', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'TOTAL ABSEN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'LEMBUR RUTIN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(50, 0, 'LEMBUR BIASA / NERUS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'LEMBUR OFF', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'IZIN', 1, 0, 'C', 0, '', 0);
        PDF::Ln();

        $count = sizeof($data);
        if ($count > 0) {
            $checkInvoice = '';
            $grandTotal = 0;
            $total_setelah_dipot = 0;
            $total_upah = 0;
            $sum_pot_koperasi = 0;

            foreach ($data as $item) {
                $total_absensi = AbsensiHarian::select('absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.nik', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi', 'absensi_harians.upah_harian', 'karyawans.nik', 'karyawans.nama', 'karyawans.norek', 'karyawans.nilai_upah', 'karyawans.pot_koperasi', 'karyawans.tunjangan', 'karyawans.tgl_masuk', 'karyawans.uang_makan')
                ->join('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                ->where('karyawans.nik', '=', $item->nik)
                ->where('absensi_harians.status', '=', 2)
                ->where('karyawans.status_karyawan_id', '=', 1)
                ->where('absensi_harians.jam_kerja', '!=', 'LEMBUR')
                ->count('karyawans.nik');

                //  1=rutin 2= biasa 3= off

                $total_lembur_rutin = DB::table('absensi_harians')
                    ->select('*')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                     ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('absensi_harians.jenis_lembur', '=', 1)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 1)
                    ->sum('absensi_harians.konfirmasi_lembur');
                $lembur_rutin = 14200 * $total_lembur_rutin;

                $total_lembur_biasa = DB::table('absensi_harians')
                    ->select('*')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                     ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('absensi_harians.jenis_lembur', '=', 2)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 1)
                    ->sum('absensi_harians.konfirmasi_lembur');

                $lembur_biasa = 21300 * $total_lembur_biasa;

                $total_lembur_off = DB::table('absensi_harians')
                    ->select('*')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                     ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('absensi_harians.jenis_lembur', '=', 3)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 1)
                    ->sum('absensi_harians.konfirmasi_lembur');

                $lembur_off = 28400 * $total_lembur_off;

                $total_pot_absensi = DB::table('absensi_harians')
                ->where('absensi_harians.karyawan_id', '=', $item->nik)
                ->where('absensi_harians.status', '=', 2)
                ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                ->sum('pot_absensi');

                //HITUNG JUMLAH TIDAK MASUK KERJA
                $hari_off = DB::table('absensi_harians')
                ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                ->whereNull('jml_kehadiran')
                ->whereNull('jam_lembur')
                ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                ->where('absensi_harians.status', '=', 2)
                ->where('karyawans.status_karyawan_id', '=', 1)
                ->where('karyawans.nik', '=', $item->nik)
                ->count('absensi_harians.id');

                $hari_kerja = DB::table('absensi_harians')
                ->where('jml_kehadiran', '!=', '00:00:00')
                ->where('absensi_harians.karyawan_id', '=', $item->nik)
                ->where('absensi_harians.status', '=', 2)
                ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                ->count('jml_kehadiran');

                //CEK DIA PUNYA TUNJANGAN GA
                if ($item->tunjangan !=0) {
                // PERHITUNGAN POTONGAN JABATAN
                    $pot_jabatan = (0.25 * $item->tunjangan) * $hari_off;
                    $pot_umk = 0;
                } else {
                // PERHITUNGAN POTONGAN UMK
                    $pot_umk = 50000 * $hari_off;
                    $pot_jabatan = 0;
                }
                // PERHITUNGAN POTONGAN BPJS JIKA DIHITUNG
                if ($potongan != 'bpjs') {
                    $item->pot_bpjs = 0;
                }

                $uang_makan = $hari_kerja * $item->uang_makan;



                $sum_pot_absensi = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                     ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 1)
                    ->sum('absensi_harians.pot_absensi');

                // PERHITUNGAN TOTAL
                $total_upah_harian = ($item->nilai_upah + $item->tunjangan + $lembur_rutin + $uang_makan + $lembur_biasa + $lembur_off) - ($pot_jabatan + $pot_umk + $total_pot_absensi + $item->pot_bpjs + $item->pot_koperasi);

                PDF::Cell(40, 0, $item->nama, 1, 0, 'L', 0, '', 1);
                PDF::Cell(40, 0, $item->tgl_masuk, 1, 0, 'R', 0, '', 1);
                PDF::Cell(50, 0, $item->norek, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, number_format($item->nilai_upah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(45, 0, number_format($item->pot_koperasi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(40, 0, number_format($total_pot_absensi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(40, 0, number_format($item->pot_bpjs, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(35, 0, number_format($total_upah_harian, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $total_absensi, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $total_lembur_rutin, 1, 0, 'R', 0, '', 1);
                PDF::Cell(50, 0, $total_lembur_biasa, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $total_lembur_off, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $hari_off, 1, 0, 'R', 0, '', 1);
                PDF::Ln();

                $total_setelah_dipot += $total_upah_harian;
                $total_upah += $item->nilai_upah;
                $sum_pot_koperasi += $item->pot_koperasi;
                //$checkInvoice = $item->no_invoice;
            }
            PDF::SetFont('', 'B', 10);
            // grand total
            PDF::Cell(130, 0, 'TOTAL ', 1, 0, 'R', 0, '', 1);
            PDF::Cell(30, 0, number_format($total_upah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(45, 0, number_format($sum_pot_koperasi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(40, 0, number_format($sum_pot_absensi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(75, 0, number_format($total_setelah_dipot, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(170, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 10);
        } else {
            PDF::Cell(22, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(26, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(50, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(12, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(12, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(20, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);

            PDF::Ln();
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('laporan_absensi_karyawan_tetap.pdf');

        // need to call exit, i don't know why
        exit;
    }

    public function absensiKaryawanHarian()
    {
        $data['default_date'] = date('d-m-Y');

        return view('report.absensi_karyawan_harian_params', $data);
    }

    // preview
    public function previewAbsensiKaryawanHarian($tanggal_awal, $tanggal_akhir, $potongan)
    {
        $tgl_awal = Carbon::createFromFormat('d-m-Y', $tanggal_awal)->format('Y-m-d');
        $tgl_akhir = Carbon::createFromFormat('d-m-Y', $tanggal_akhir)->format('Y-m-d');

        $data = AbsensiHarian::select('absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.nik', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi', 'absensi_harians.upah_harian', 'karyawans.nik', 'karyawans.nama', 'karyawans.norek', 'karyawans.nilai_upah', 'karyawans.pot_koperasi', 'karyawans.tgl_masuk', 'karyawans.pot_bpjs')
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
        ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
        ->where('absensi_harians.status', '=', 2)
        ->where('karyawans.status_karyawan_id', '=', 2)
        ->groupBy('karyawans.nik')
        ->get();

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Absensi - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Absensi Harian');
        PDF::SetKeywords('Laporan Absensi Harian');

        PDF::setFooterCallback(function ($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
        });

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('P', 'A2');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10, 15);

        PDF::SetFont('times', 'B', 14);

        //PDF::setXY(54, 10);
        PDF::setX(15);
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        //PDF::setXY(54, 16);
        PDF::setX(15);
        PDF::SetFont('', '', 10);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15 Bandung, Telp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);

         // Line ($x1, $y1, $x2, $y2, $style=array())
        $style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        PDF::Line(15, 21, 576, 21); // $y2 = 282 for A4
        PDF::Line(15, 22, 576, 22, $style); // $y2 = 402 for A3
        PDF::setLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        PDF::Ln(12);

        PDF::SetFont('', 'B', 14);

        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        if ($potongan == '0') {
            $ket = '';
        } else {
            $ket = '(termasuk potongan '.$potongan.' )';
        }
        PDF::Cell(0, 0, 'LAPORAN ABSENSI PEGAWAI HARIAN '.$ket, 0, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::SetFont('', '', 14);

        PDF::Cell(360, 0, 'PERIODE', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        PDF::Cell(360, 0, $tanggal_awal.' hingga '.$tanggal_akhir, 1, 0, 'C', 0, '', 0);

        PDF::Ln(8);

        PDF::Cell(180, 5, 'NAMA KARYAWAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(180, 5, 'TOTAL UPAH', 1, 0, 'C', 0, '', 0);

        PDF::Ln();

        $count = sizeof($data);
        if ($count > 0) {
            $checkInvoice = '';

            $t_upah = 0;
            foreach ($data as $item) {
                $total = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 2)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->sum('absensi_harians.upah_harian');

                $total_pot_absensi = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereBetween('absensi_harians.tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 2)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->sum('absensi_harians.pot_absensi');


                // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0,
                PDF::SetFont('', 'B', 14);
                PDF::Cell(180, 5, $item->nama, 1, 0, 'L', 0, '', 1);

                if ($potongan == 'bpjs') {
                    $total = $total - $item->pot_bpjs;
                    PDF::Cell(180, 5, number_format($total, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                } else {
                    //$total = $total - $total_pot_absensi;
                    PDF::Cell(180, 5, number_format($total, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                }
                PDF::Ln();
                $t_upah = $t_upah + $total;
            }

            //dd($t_upah);
            PDF::SetFont('', 'B', 14);
            // grand total

            PDF::Cell(180, 5, 'TOTAL ', 1, 0, 'R', 0, '', 1);
            if ($potongan == 'bpjs') {
                PDF::Cell(180, 5, number_format($t_upah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            } else {
                PDF::Cell(180, 5, number_format($t_upah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            }

            PDF::Ln();
            PDF::SetFont('', '', 10);
        } else {
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);

            PDF::Ln();
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('laporan_absensi_karyawan_harian.pdf');

        // need to call exit, i don't know why
        exit;
    }

    public function absensiKaryawanPacking()
    {
        $data['default_date'] = date('d-m-Y');

        return view('report.absensi_karyawan_packing_params', $data);
    }

    // preview
    public function previewAbsensiKaryawanPacking($tanggal_awal, $tanggal_akhir)
    {
        $tgl_awal = Carbon::createFromFormat('d-m-Y', $tanggal_awal)->format('Y-m-d');
        $tgl_akhir = Carbon::createFromFormat('d-m-Y', $tanggal_akhir)->format('Y-m-d');

        $absensi_packings = AbsensiPacking::select('*')
        ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();

        $jenises = DB::select('select * from report_jenis');

        $report_jenises = DB::select("
                select rb.nama, ap.tanggal, ifnull(sum(jumlah), 0) as jumlah
                from report_jenis rb
                left join absensi_packings ap on ap.jenis = rb.id and ap.bagian = 'A'
                where ap.tanggal >= :awal and ap.tanggal <= :akhir
                group by rb.nama, ap.tanggal
            ", ['awal' => $tgl_awal, 'akhir' => $tgl_akhir]);

        $report_jenises_B = DB::select("
                select rb.nama, ap.tanggal, ifnull(sum(jumlah), 0) as jumlah
                from report_jenis rb
                left join absensi_packings ap on ap.jenis = rb.id and ap.bagian = 'B'
                where ap.tanggal >= :awal and ap.tanggal <= :akhir
                group by rb.nama, ap.tanggal
            ", ['awal' => $tgl_awal, 'akhir' => $tgl_akhir]);

        $report_jenises_C = DB::select("
                select rb.nama, ap.tanggal, ifnull(sum(jumlah), 0) as jumlah
                from report_jenis rb
                left join absensi_packings ap on ap.jenis = rb.id and ap.bagian = 'C'
                where ap.tanggal >= :awal and ap.tanggal <= :akhir
                group by rb.nama, ap.tanggal
            ", ['awal' => $tgl_awal, 'akhir' => $tgl_akhir]);

        $report_jenises_total = DB::select('
                select ap.bagian, rj.nama as jenis, sum(jumlah) jumlah, upah, sum(jumlah)*upah as hasil
                from absensi_packings ap
                join report_jenis rj on ap.jenis = rj.id
                where ap.tanggal >= :awal and ap.tanggal <= :akhir
                group by ap.bagian, ap.jenis
            ', ['awal' => $tgl_awal, 'akhir' => $tgl_akhir]);

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Absensi - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Absensi Harian');
        PDF::SetKeywords('Laporan Absensi Harian');

        PDF::setFooterCallback(function ($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
        });

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('L', 'A2');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10, 15);

        PDF::SetFont('times', 'B', 12);

        //PDF::setXY(54, 10);
        PDF::setX(15);
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        //PDF::setXY(54, 16);
        PDF::setX(15);
        PDF::SetFont('', '', 10);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15 Bandung, Telp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);

         // Line ($x1, $y1, $x2, $y2, $style=array())
        $style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        PDF::Line(15, 21, 576, 21); // $y2 = 282 for A4
        PDF::Line(15, 22, 576, 22, $style); // $y2 = 402 for A3
        PDF::setLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        PDF::Ln(12);

        PDF::SetFont('', 'B', 12);

        PDF::Cell(0, 0, 'LAPORAN ABSENSI PEGAWAI HARIAN PACKING', 0, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::SetFont('', '', 10);

        PDF::Cell(544, 0, 'PERIODE', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        PDF::Cell(544, 0, $tanggal_awal.' hingga '.$tanggal_akhir, 1, 0, 'C', 0, '', 0);

        PDF::Ln();

        PDF::Cell(40, 0, 'BAGIAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(168, 0, 'MEJA A', 1, 0, 'C', 0, '', 0);
        PDF::Cell(168, 0, 'MEJA B', 1, 0, 'C', 0, '', 0);
        PDF::Cell(168, 0, 'MEJA C', 1, 0, 'C', 0, '', 0);

        PDF::Ln();

        PDF::Cell(40, 0, 'Tanggal', 1, 0, 'C', 0, '', 0);
        foreach ($jenises as $jenis) {
            PDF::Cell(12, 0, $jenis->nama, 1, 0, 'C', 0, '', 0);
        }

        foreach ($jenises as $jenis) {
            PDF::Cell(12, 0, $jenis->nama, 1, 0, 'C', 0, '', 0);
        }

        foreach ($jenises as $jenis) {
            PDF::Cell(12, 0, $jenis->nama, 1, 0, 'C', 0, '', 0);
        }

        $count = sizeof($absensi_packings);
        if ($count > 0) {
            $checkInvoice = '';

            PDF::Ln();
            foreach ($absensi_packings as $absensi_packing) {
                PDF::Cell(40, 0, $absensi_packing->tanggal, 1, 0, 'L', 0, '', 1);

                foreach ($jenises as $jenis) {
                    $found = false;
                    foreach ($report_jenises as $report_jenis) {
                        if ($jenis->nama == $report_jenis->nama && $report_jenis->tanggal == $absensi_packing->tanggal) {
                            PDF::Cell(12, 0, $report_jenis->jumlah, 1, 0, 'C', 0, '', 0);
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        PDF::Cell(12, 0, 0, 1, 0, 'C', 0, '', 0);
                    }
                }

                foreach ($jenises as $jenis) {
                    $found = false;
                    foreach ($report_jenises_B as $report_jenis_B) {
                        if ($jenis->nama == $report_jenis_B->nama && $report_jenis_B->tanggal == $absensi_packing->tanggal) {
                            PDF::Cell(12, 0, $report_jenis_B->jumlah, 1, 0, 'C', 0, '', 0);
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        PDF::Cell(12, 0, 0, 1, 0, 'C', 0, '', 0);
                    }
                }

                foreach ($jenises as $jenis) {
                    $found = false;
                    foreach ($report_jenises_C as $report_jenis_C) {
                        if ($jenis->nama == $report_jenis_C->nama && $report_jenis_C->tanggal == $absensi_packing->tanggal) {
                            PDF::Cell(12, 0, $report_jenis_C->jumlah, 1, 0, 'C', 0, '', 0);
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        PDF::Cell(12, 0, 0, 1, 0, 'C', 0, '', 0);
                    }
                }

                PDF::Ln();

                //$checkInvoice = $item->no_invoice;
            }

            PDF::SetFont('', 'B', 10);
            // grand total
            PDF::Cell(40, 0, 'Jumlah', 1, 0, 'R', 0, '', 1);

            //TOTAL MEJA A
            $total_upah_a = 0;
            foreach ($jenises as $jenis) {
                $found = false;
                foreach ($report_jenises_total as $r) {
                    if ($jenis->nama == $r->jenis && $r->bagian == 'A') {
                        $total_upah_a += $r->hasil;

                        PDF::Cell(12, 0, $r->jumlah, 1, 0, 'C', 0, '', 0);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    PDF::Cell(12, 0, 0, 1, 0, 'C', 0, '', 0);
                }
            }

            //TOTAL MEJA B
            $total_upah_b = 0;
            foreach ($jenises as $jenis) {
                $found = false;
                foreach ($report_jenises_total as $r) {
                    if ($jenis->nama == $r->jenis && $r->bagian == 'B') {
                        $total_upah_b += $r->hasil;

                        PDF::Cell(12, 0, $r->jumlah, 1, 0, 'C', 0, '', 0);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    PDF::Cell(12, 0, 0, 1, 0, 'C', 0, '', 0);
                }
            }

            //TOTAL MEJA C
            $total_upah_c = 0;
            foreach ($jenises as $jenis) {
                $found = false;
                foreach ($report_jenises_total as $r) {
                    if ($jenis->nama == $r->jenis && $r->bagian == 'C') {
                        $total_upah_c += $r->hasil;
                        PDF::Cell(12, 0, $r->jumlah, 1, 0, 'C', 0, '', 0);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    PDF::Cell(12, 0, 0, 1, 0, 'C', 0, '', 0);
                }
            }

            PDF::Ln();

            PDF::SetFont('', 'B', 10);
            // grand total
            PDF::Cell(40, 0, 'Upah', 1, 0, 'R', 0, '', 1);
            PDF::Cell(168, 0, number_format($total_upah_a, 0, '.', ','), 1, 0, 'C', 0, '', 1);
            PDF::Cell(168, 0, number_format($total_upah_b, 0, '.', ','), 1, 0, 'C', 0, '', 1);
            PDF::Cell(168, 0, number_format($total_upah_c, 0, '.', ','), 1, 0, 'C', 0, '', 1);
        } else {
            // PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            // PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            // PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);

            PDF::Ln();
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('laporan_absensi_karyawan_harian.pdf');

        // need to call exit, i don't know why
        exit;
    }

    public function absensiKaryawanStaff()
    {
        $data['default_date'] = date('d-m-Y');

        return view('report.absensi_karyawan_staff_params', $data);
    }

    // preview
    public function previewAbsensiKaryawanStaff($bulan)
    {
        $tahun_skrg = date('Y');
        //$tahun_skrg = 2016;

        $bulanLbl = '';
        switch ($bulan) {
            case 1:
                $bulanLbl = 'Januari';
                break;
            case 2:
                $bulanLbl = 'Februari';
                break;
            case 3:
                $bulanLbl = 'Maret';
                break;
            case 4:
                $bulanLbl = 'April';
                break;
            case 5:
                $bulanLbl = 'Mei';
                break;
            case 6:
                $bulanLbl = 'Juni';
                break;
            case 7:
                $bulanLbl = 'Juli';
                break;
            case 8:
                $bulanLbl = 'Agustus';
                break;
            case 9:
                $bulanLbl = 'September';
                break;
            case 10:
                $bulanLbl = 'Oktober';
                break;
            case 11:
                $bulanLbl = 'November';
                break;
            case 12:
                $bulanLbl = 'Desember';
                break;
        }

        $data = AbsensiHarian::select('absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.nik', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi', 'karyawans.nik', 'karyawans.nama', 'karyawans.norek', 'karyawans.nilai_upah', 'karyawans.pot_koperasi', 'karyawans.tunjangan', 'karyawans.tgl_masuk')
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
        ->whereMonth('absensi_harians.tanggal', '=', $bulan)
        ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
        ->where('absensi_harians.status', '=', 2)
        ->where('karyawans.status_karyawan_id', '=', 3)
        ->groupBy('karyawans.nik')
        ->get();

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Absensi Pegawai Staff - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Absensi Pegawai Staff');
        PDF::SetKeywords('Laporan Penjualan Trimitra Kemasindo');

        PDF::setFooterCallback(function ($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
        });

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('L', 'A2');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10, 15);

        // Image ($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array())
        //PDF::Image(asset('/image/bmt.png'), 15, 5, 35, 15, '', '', 'T', true);

        // SetFont ($family, $style='', $size=null, $fontfile='', $subset='default', $out=true)
        PDF::SetFont('times', 'B', 12);

        //PDF::setXY(54, 10);
        PDF::setX(15);
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        //PDF::setXY(54, 16);
        PDF::setX(15);
        PDF::SetFont('', '', 10);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15 Bandung, Telp. (022) 87304121, Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);

         // Line ($x1, $y1, $x2, $y2, $style=array())
        $style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        PDF::Line(15, 21, 576, 21); // $y2 = 282 for A4
        PDF::Line(15, 22, 576, 22, $style); // $y2 = 402 for A3
        PDF::setLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        PDF::Ln(12);

        PDF::SetFont('', 'B', 12);

        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        PDF::Cell(0, 0, 'LAPORAN ABSENSI PEGAWAI STAFF', 0, 0, 'C', 0, '', 0);
        PDF::Ln(8);

        PDF::SetFont('', '', 10);

        PDF::Cell(30, 0, 'BULAN', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        PDF::Cell(30, 0, $bulanLbl, 1, 0, 'C', 0, '', 0);

        PDF::Ln(8);

        PDF::Cell(40, 0, 'NAMA KARYAWAN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'TGL. MASUK KERJA', 1, 0, 'C', 0, '', 0);
        PDF::Cell(50, 0, 'NO. REKENING', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'TOTAL UPAH', 1, 0, 'C', 0, '', 0);
        PDF::Cell(45, 0, 'POTONGAN KOPERASI', 1, 0, 'C', 0, '', 0);
        PDF::Cell(40, 0, 'POTONGAN ABSENSI', 1, 0, 'C', 0, '', 0);
        PDF::Cell(35, 0, 'SETELAH DI POT', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'TOTAL ABSEN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'LEMBUR RUTIN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(50, 0, 'LEMBUR BIASA / NERUS', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'LEMBUR OFF', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'SAKIT', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'IZIN', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'ALFA', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 0, 'CUTI', 1, 0, 'C', 0, '', 0);
        PDF::Ln();

        $count = sizeof($data);
        if ($count > 0) {
            $checkInvoice = '';
            $grandTotal = 0;
            $total_setelah_dipot = 0;
            foreach ($data as $item) {
                $total_absensi = AbsensiHarian::select('absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'karyawans.nik', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status', 'absensi_harians.pot_absensi', 'absensi_harians.upah_harian', 'karyawans.nik', 'karyawans.nama', 'karyawans.norek', 'karyawans.nilai_upah', 'karyawans.pot_koperasi', 'karyawans.tgl_masuk')
                ->join('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                ->where('karyawans.nik', '=', $item->nik)
                ->where('absensi_harians.status', '=', 2)
                ->where('karyawans.status_karyawan_id', '=', 3)
                ->whereNotNull('absensi_harians.scan_masuk')
                ->where('absensi_harians.jam_kerja', '!=', 'LEMBUR')
                ->count('karyawans.nik');

                //  1=rutin 2= biasa 3= off

                $data_lembur_rutin = DB::table('absensi_harians')
                    ->select('*')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                    ->where('absensi_harians.jenis_lembur', '=', 1)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->count('karyawans.id');

                $data_lembur_biasa = DB::table('absensi_harians')
                    ->select('*')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                    ->where('absensi_harians.jenis_lembur', '=', 2)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->count('karyawans.nik');

                $data_lembur_off = DB::table('absensi_harians')
                    ->select('*')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                    ->where('absensi_harians.jenis_lembur', '=', 3)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->count('karyawans.nik');

                $pot_absensi = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->sum('absensi_harians.pot_absensi');
                //DB::connection()->enableQueryLog();
                $total_upah_harian = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->where('karyawans.nik', '=', $item->nik)
                    ->sum('absensi_harians.upah_harian');
                // $queries = DB::getQueryLog();

                // dd($queries);

                //HITUNG JUMLAH TIDAK MASUK KERJA
                $hari_off = DB::table('absensi_harians')
                ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                ->whereNull('jml_kehadiran')
                ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                ->where('absensi_harians.status', '=', 2)
                ->where('karyawans.status_karyawan_id', '=', 3)
                ->where('karyawans.nik', '=', $item->nik)
                ->count('absensi_harians.id');

                // PERHITUNGAN POTONGAN UMK
                // if (is_null($item->scan_masuk)) {
                //     $pot_umk = ($item->tunjangan * 0.25);
                // } else {
                //     $pot_umk = 0;
                // }

                $pot_umk = 0;

                $setelah_dipot = $total_upah_harian - $pot_umk - $item->pot_koperasi - $item->pot_bpjs;

                $total_upah = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->whereYear('absensi_harians.tanggal', '=', $tahun_skrg)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->sum('absensi_harians.upah_harian');

                $total_pot_koperasi = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->sum('karyawans.pot_koperasi');

                $total_pot_absensi = DB::table('absensi_harians')
                    ->select('absensi_harians.pot_absensi')
                    ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
                    ->whereMonth('absensi_harians.tanggal', '=', $bulan)
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.status_karyawan_id', '=', 3)
                    ->sum('absensi_harians.pot_absensi');

                PDF::Cell(40, 0, $item->nama, 1, 0, 'L', 0, '', 1);
                PDF::Cell(40, 0, $item->tgl_masuk, 1, 0, 'R', 0, '', 1);
                PDF::Cell(50, 0, $item->norek, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, number_format($total_upah_harian, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(45, 0, number_format($item->pot_koperasi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(40, 0, number_format($pot_absensi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(35, 0, number_format($setelah_dipot, 0, '.', ','), 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $total_absensi, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $data_lembur_rutin, 1, 0, 'R', 0, '', 1);
                PDF::Cell(50, 0, $data_lembur_biasa, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $data_lembur_off, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, 0, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, $hari_off, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, 0, 1, 0, 'R', 0, '', 1);
                PDF::Cell(30, 0, 0, 1, 0, 'R', 0, '', 1);
                PDF::Ln();
                $total_setelah_dipot += $setelah_dipot;
                //$checkInvoice = $item->no_invoice;
            }

            //dd($setelah_dipot++);
            PDF::SetFont('', 'B', 10);
            // grand total
            PDF::Cell(130, 0, 'TOTAL ', 1, 0, 'R', 0, '', 1);
            PDF::Cell(30, 0, number_format($total_upah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(45, 0, number_format($total_pot_koperasi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(40, 0, number_format($total_pot_absensi, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(35, 0, number_format($total_setelah_dipot, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(260, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Ln();
            PDF::SetFont('', '', 10);
        } else {
            PDF::Cell(22, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(26, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(50, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(30, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(40, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(12, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(12, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(20, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);
            PDF::Cell(24, 0, '', 1, 0, 'C', 0, '', 0);

            PDF::Ln();
        }

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('laporan_absensi_karyawan_staff.pdf');

        // need to call exit, i don't know why
        exit;
    }
}
