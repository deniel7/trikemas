<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Karyawan;
use App\StatusKaryawan;
use Datatables;
use Carbon\Carbon;
use PDF;

class KaryawanHarianController extends Controller
{
    public function index()
    {
        if (in_array(120, session()->get('allowed_menus'))) {
            return view('karyawan_harian.index');
        } else {
        }
    }

    public function datatable()
    {
        $karyawan_harians = DB::table('karyawans')
        ->select(['karyawans.id', 'karyawans.status_karyawan_id', 'status_karyawans.keterangan', 'karyawans.nik', 'karyawans.nama', 'karyawans.alamat', 'karyawans.phone', 'karyawans.lulusan', 'karyawans.tgl_masuk', 'karyawans.nilai_upah', 'karyawans.uang_makan', 'karyawans.pot_koperasi', 'karyawans.pot_bpjs', 'karyawans.tunjangan', 'karyawans.norek'])
        ->join('status_karyawans', 'karyawans.status_karyawan_id', '=', 'status_karyawans.id')
        ->where('karyawans.status_karyawan_id', '=', 2);

        return Datatables::of($karyawan_harians)

        ->editColumn('status_karyawan_id', '<span class="pull-right">{{ App\Karyawan::find($id)->statusKaryawan->keterangan }}</span>')
        ->editColumn('nama', '<span class="pull-right">{{ $nama }}</span>')
        ->editColumn('alamat', '<span class="pull-right">{{ $alamat }}</span>')
        ->editColumn('phone', '<span class="pull-right">{{ $phone }}</span>')
        ->editColumn('lulusan', '<span class="pull-right">{{ $lulusan }}</span>')
        ->editColumn('tgl_masuk', '<span class="pull-right">{{ $tgl_masuk }}</span>')
        ->editColumn('nilai_upah', '<span class="pull-right">{{ number_format($nilai_upah,0,".",",") }}</span>')

        ->editColumn('uang_makan', '<span class="pull-right">{{ number_format($uang_makan,0,".",",") }}</span>')

        ->editColumn('tunjangan', '<span class="pull-right">{{ number_format($tunjangan,0,".",",") }}</span>')

        ->editColumn('pot_koperasi', '<span class="pull-right">{{ number_format($pot_koperasi,0,".",",") }}</span>')

        ->editColumn('pot_bpjs', '<span class="pull-right">{{ number_format($pot_bpjs,0,".",",") }}</span>')

        ->editColumn('norek', '<span class="pull-right">{{ $norek }}</span>')
        ->addColumn('action', function ($karyawan_harian) {

            $html = '<div class="text-center btn-group btn-group-justified">';

            if (in_array(125, session()->get('allowed_menus'))) {
                $html .= '<a href="javascript:;" onClick="karyawanHarianModule.showPrint('.$karyawan_harian->id.');"><button type="button" class="btn btn-sm"><i class="fa fa-print"></i></button></a>';
            }
            if (in_array(122, session()->get('allowed_menus'))) {
                $html .= '<a href="karyawan-tetap/'.$karyawan_harian->id.'/edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a>';
            }
            if (in_array(123, session()->get('allowed_menus'))) {
                $html .= '<a href="javascript:;" onclick="karyawanHarianModule.confirmDelete(event, \''.$karyawan_harian->id.'\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
            }
            $html .= '</div>';

            return $html;
        })

        ->make(true);
    }

    public function show($karyawan_harian)
    {
        $id = $karyawan_harian->id;

        $details = DB::table('karyawans')
        ->select('karyawans.id', 'nik', 'nama', 'norek', 'status_karyawans.keterangan')
        ->join('status_karyawans', 'status_karyawans.id', '=', 'karyawans.status_karyawan_id')
        ->where('karyawans.id', '=', $id)
        ->get();

        // $test = Karyawan::find($id);
        if (count($details) == 1) {
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

    public function doPrint(Request $request)
    {
        $id = $request->input('id');
        $start_date = $request->input('dari');
        $end_date = $request->input('ke');

        $karyawan = Karyawan::find($id);

        $gaji = $karyawan->nilai_upah;

        //HITUNG JUMLAH MASUK KERJA
        $hari_kerja = DB::table('absensi_harians')
        ->where('jml_kehadiran', '!=', '00:00:00')
        ->where('absensi_harians.karyawan_id', '=', $id)
        ->where('absensi_harians.status', '=', 2)
        ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
        ->count('jml_kehadiran');

        $nilai_upah = $hari_kerja * $gaji;

        //HITUNG JUMLAH TIDAK MASUK KERJA
        $hari_off = DB::table('absensi_harians')
        ->where('jml_kehadiran', '=', '00:00:00')
        ->where('absensi_harians.karyawan_id', '=', $id)
        ->where('absensi_harians.status', '=', 2)
        ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
        ->count('jml_kehadiran');

        // HITUNG UANG MAKAN
        $uang_makan = $hari_kerja * $karyawan->uang_makan;

        // PERHITUNGAN LEMBUR
        $total_lembur_rutin = DB::table('absensi_harians')
        ->where('absensi_harians.karyawan_id', '=', $id)
        ->where('absensi_harians.jenis_lembur', '=', 1)
        ->where('absensi_harians.status', '=', 2)
        ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
        ->sum('konfirmasi_lembur');

        $lembur_rutin = $total_lembur_rutin * 10500;

        $total_lembur_biasa = DB::table('absensi_harians')
        ->where('absensi_harians.karyawan_id', '=', $id)
        ->where('absensi_harians.jenis_lembur', '=', 2)
        ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
        ->sum('konfirmasi_lembur');

        $lembur_biasa = $total_lembur_biasa * 15700;

        // PENJUMLAHAN POTONGAN ABSENSI
        $total_pot_absensi = DB::table('absensi_harians')
        ->where('absensi_harians.karyawan_id', '=', $id)
        ->where('absensi_harians.status', '=', 2)
        ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
        ->sum('pot_absensi');

        $total_upah_harian = DB::table('absensi_harians')
                    ->leftjoin('karyawans', 'karyawans.id', '=', 'absensi_harians.karyawan_id')
                    ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
                    ->where('absensi_harians.status', '=', 2)
                    ->where('karyawans.id', '=', $id)
                    ->sum('absensi_harians.upah_harian');
        //dd($total_upah_harian);
        // PERHITUNGAN TOTAL
        $total = ($nilai_upah + $uang_makan + $lembur_rutin + $lembur_biasa) - $karyawan->pot_bpjs - $total_pot_absensi;

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Print Slip Gaji - Trimitra Kemasindo');
        PDF::SetSubject('Slip Gaji Karyawan');
        PDF::SetKeywords('Slip Gaji Karyawan Trimitra Kemasindo');

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('P', 'A4');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10);

        PDF::setX(15);

        PDF::SetFont('times', 'B', 11);

        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::SetFont('', '', 9);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(0, 0, 'Tlp. (022) 87304121', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(0, 0, 'Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(0, 0, 'Bandung', 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::setXY(80, 14);

        PDF::Ln(20);

        $curY = PDF::getY();
        PDF::Ln();

        $no = 1;

        PDF::Ln();

        PDF::setY($curY);

        PDF::SetFont('', '', 9);
        PDF::Cell(90, 0, 'Periode (Harian):', 0, 'L', false, 0);
        PDF::Cell(0, 0, ' '.$start_date.' s/d '.$end_date, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::SetFont('', '', 9);
        PDF::Cell(90, 0, 'Tanggal Cetak :', 0, 'L', false, 0);
        PDF::Cell(0, 0, ' '.date('d-m-Y h:m'), 0, 0, 'L', 0, '', 0);
        PDF::Ln(7);

        PDF::Cell(90, 0, 'Nama', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->nama, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Bagian', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->bagian, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Upah', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($nilai_upah, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Uang Makan', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($uang_makan, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln(7);

        PDF::Cell(90, 0, 'Lbr. Rutin', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($lembur_rutin, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Lbr. Biasa', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($lembur_biasa, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln(12);

        PDF::Cell(90, 0, 'Potongan Absensi', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($total_pot_absensi, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Potongan BPJS', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($karyawan->pot_bpjs, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Total', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.number_format($total, 0, '.', ','), 0, 0, 'L', 0, '', 0);
        PDF::Ln(8);

        PDF::Cell(120, 0, 'Penerima', 0, 0, 'L', 0, '', 0);
        PDF::Ln(16);
        PDF::Cell(120, 0, '(               )', 0, 0, 'L', 0, '', 0);

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('slip_gaji.pdf');

        // need to call exit, i don't know why
        exit;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($karyawan_harian)
    {
        DB::beginTransaction();

        try {
            $karyawan_harian->delete();

            DB::commit();
            echo 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            echo 'Error ('.$e->errorInfo[1].'): '.$e->errorInfo[2].'.';
        }
    }
}
