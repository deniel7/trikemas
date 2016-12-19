<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Karyawan;
use App\StatusKaryawan;
use Datatables;
use Carbon\Carbon;
use PDF;

class KaryawanTetapController extends Controller
{
    public function index()
    {
        return view('karyawan.index');
    }

    public function datatable()
    {
        $karyawans = DB::table('karyawans')
        ->select(['karyawans.id', 'status_karyawans.keterangan', 'karyawans.nik', 'karyawans.nama', 'karyawans.alamat', 'karyawans.phone', 'karyawans.lulusan', 'karyawans.tgl_masuk', 'karyawans.nilai_upah', 'karyawans.uang_makan', 'karyawans.uang_lembur', 'karyawans.tunjangan', 'karyawans.norek'])
        ->join('status_karyawans', 'karyawans.status_karyawan_id', '=', 'status_karyawans.id')
        ->where('karyawans.status_karyawan_id', '=', 1);

        return Datatables::of($karyawans)

        ->editColumn('status_karyawan_id', '<span class="pull-right">{{ App\Karyawan::find($id)->statusKaryawan->keterangan }}</span>')
        ->editColumn('nama', '<span class="pull-right">{{ $nama }}</span>')
        ->editColumn('alamat', '<span class="pull-right">{{ $alamat }}</span>')
        ->editColumn('phone', '<span class="pull-right">{{ $phone }}</span>')
        ->editColumn('lulusan', '<span class="pull-right">{{ $lulusan }}</span>')
        ->editColumn('tgl_masuk', '<span class="pull-right">{{ $tgl_masuk }}</span>')

        ->editColumn('nilai_upah', '<span class="pull-right">{{ number_format($nilai_upah,0,".",",") }}</span>')

        ->editColumn('uang_makan', '<span class="pull-right">{{ number_format($uang_makan,0,".",",") }}</span>')

        ->editColumn('uang_lembur', '<span class="pull-right">{{ number_format($uang_lembur,0,".",",") }}</span>')

        ->editColumn('tunjangan', '<span class="pull-right">{{ number_format($tunjangan,0,".",",") }}</span>')

        ->editColumn('norek', '<span class="pull-right">{{ $norek }}</span>')
        ->addColumn('action', function ($karyawan) {

            $html = '<div class="text-center btn-group btn-group-justified">';

            $html .= '<a href="karyawan-tetap/'.$karyawan->id.'/edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a>';
            $html .= '<a href="javascript:;" onClick="karyawanModule.showPrint('.$karyawan->id.');"><button type="button" class="btn btn-sm"><i class="fa fa-print"></i></button></a>';
            $html .= '</div>';

            return $html;
        })
        ->make(true);
    }

    public function create()
    {
        $data['status_karyawans'] = StatusKaryawan::select('id', 'keterangan')->orderBy('id')->get();

        return view('karyawan/create', $data);
    }

    public function store(Request $request)
    {
        $nilai_upah = str_replace(',', '', $request->input('nilai_upah'));
        //$nilai_upah_ = str_replace('Rp', '', $nilai_upah);
        $uang_makan = str_replace(',', '', $request->input('uang_makan'));
        //$uang_makan_ = str_replace('Rp', '', $uang_makan);
        $uang_lembur = str_replace(',', '', $request->input('uang_lembur'));
        //$uang_lembur_ = str_replace('Rp', '', $uang_lembur);

        $karyawan = new Karyawan();
        $karyawan->status_karyawan_id = $request->input('status_karyawan_id');
        $karyawan->nama = $request->input('nama');
        $karyawan->alamat = $request->input('alamat');
        $karyawan->phone = $request->input('phone');
        $karyawan->lulusan = $request->input('lulusan');
        $karyawan->tgl_masuk = $request->input('tgl_masuk');
        $karyawan->nik = $request->input('nik');
        $karyawan->norek = $request->input('norek');

        // $karyawan->nilai_upah = $nilai_upah_;
        // $karyawan->uang_makan = $uang_makan_;
        // $karyawan->uang_lembur = $uang_lembur_;

        $karyawan->save();

        DB::commit();
        //Flash::success('Saved');

        if ($karyawan->status_karyawan_id == 1) {
            return redirect('karyawan-tetap');
        } else {
            return redirect('karyawan-harian');
        }
    }

    public function edit(Karyawan $karyawan)
    {
        $data['status_karyawans'] = StatusKaryawan::select('id', 'keterangan')->orderBy('id')->get();

        return view('karyawan/edit', compact('karyawan'), $data);
    }

    public function update(Karyawan $karyawan, Request $request)
    {
        $nilai_upah = str_replace(',', '', $request->input('nilai_upah'));
        $nilai_upah_ = str_replace('Rp', '', $nilai_upah);
        $uang_makan = str_replace(',', '', $request->input('uang_makan'));
        $uang_makan_ = str_replace('Rp', '', $uang_makan);
        $uang_lembur = str_replace(',', '', $request->input('uang_lembur'));
        $uang_lembur_ = str_replace('Rp', '', $uang_lembur);

        $karyawan->nilai_upah = $nilai_upah_;
        $karyawan->uang_makan = $uang_makan_;
        $karyawan->uang_lembur = $uang_lembur_;
        $karyawan->status_karyawan_id = $request->input('status_karyawan_id');
        $karyawan->nama = $request->input('nama');
        $karyawan->alamat = $request->input('alamat');
        $karyawan->phone = $request->input('phone');
        $karyawan->lulusan = $request->input('lulusan');
        $karyawan->tgl_masuk = $request->input('tgl_masuk');
        $karyawan->nik = $request->input('nik');
        $karyawan->norek = $request->input('norek');
        $karyawan->save();
        DB::commit();

        if ($karyawan->status_karyawan_id == 1) {
            return redirect('karyawan-tetap');
        } else {
            return redirect('karyawan-harian');
        }
    }

    public function show($karyawan_tetap)
    {
        $id = $karyawan_tetap->id;

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

        $total_jam = DB::table('absensi_harians')
        ->select(DB::raw('SUM(jam) as count'))
        // ->join('karyawans', 'karyawans.id', '=', 'absensi_harians.karyawan_id')
        ->where('absensi_harians.karyawan_id', '=', $id)
        ->whereBetween('tanggal', [new Carbon($start_date), new Carbon($end_date)])
        ->get();

        foreach ($total_jam as $total) {
            $jam = strval($total->count);
        }

        $gaji = $karyawan->nilai_upah;

        $nilai_upah = $jam * $gaji;

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Print Slip Gaji - Trimitra Kemasindo');
        PDF::SetSubject('Slip Gaji Karyawan');
        PDF::SetKeywords('Slip Gaji Karyawan Trimitra Kemasindo');

        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('P', 'A4');

        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10);

        // Image ($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array())
        //PDF::Image(asset('/image/logo-ponpes-darussalam.png'), 15, 5, 15, 15, '', '', 'T', true);
        //PDF::Image(asset('/image/bmt.png'), 15, 5, 35, 15, '', '', 'T', true);

        PDF::setX(15);

        // SetFont ($family, $style='', $size=null, $fontfile='', $subset='default', $out=true)
        PDF::SetFont('times', 'B', 11);
        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
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
        // PDF::setXY(108, $curY - 5);
        // PDF::Cell(15, 5, 'Ball', 1, 0, 'C', 0, '', 0);
        // PDF::Cell(15, 5, 'Pcs', 1, 0, 'C', 0, '', 0);
        PDF::Ln();

        $no = 1;
        // foreach ($invoice_detail as $item) {
        //     $barang = Barang::find($item->barang_id);

        //     PDF::Cell(8, 6, $no, 1, 0, 'R', 0, '', 1);
        //     PDF::Cell(25, 6, $barang->jenis, 1, 0, 'L', 0, '', 1);
        //     PDF::Cell(60, 6, $barang->nama, 1, 0, 'L', 0, '', 1);
        //     PDF::Cell(15, 6, number_format($item->jumlah_ball, 0, '.', ','), 1, 0, 'R', 0, '', 1);
        //     PDF::Cell(15, 6, number_format($item->jumlah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
        //     PDF::Cell(25, 6, number_format($item->harga_barang, 2, '.', ','), 1, 0, 'R', 0, '', 1);
        //     PDF::Cell(30, 6, number_format($item->subtotal, 2, '.', ','), 1, 0, 'R', 0, '', 1);
        //     PDF::Ln();

        //     ++$no;
        // }

        PDF::Ln();

        //MultiCell ($w, $h, $txt, $border=0, $align=‘J’, $fill=false, $ln=1, $x=“, $y=”, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign=’T’, $fitcell=false)

        PDF::setY($curY);

        PDF::SetFont('', '', 9);
        PDF::Cell(90, 0, 'Periode :', 0, 'L', false, 0);
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
        PDF::Cell(0, 0, ' '.$karyawan->nama, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Upah', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->nilai_upah, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Tunj.Jab', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->tunjangan, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Uang Makan', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_makan, 0, 0, 'L', 0, '', 0);
        PDF::Ln(7);

        PDF::Cell(90, 0, 'Lbr. Rutin', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_lembur, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Lbr. Biasa', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_makan, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Lembur Off', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_makan, 0, 0, 'L', 0, '', 0);
        PDF::Ln(7);

        PDF::Cell(90, 0, 'Potongan Jab', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_makan, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Potongan Umk', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_makan, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        PDF::Cell(90, 0, 'Potongan Koperasi', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->uang_makan, 0, 0, 'L', 0, '', 0);
        PDF::Ln(12);

        PDF::Cell(90, 0, 'Total', 0, 0, 'L', 0, '', 0);
        PDF::Cell(0, 0, ' '.$karyawan->tunjangan, 0, 0, 'L', 0, '', 0);
        PDF::Ln();

        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('slip_gaji.pdf');

        // need to call exit, i don't know why
        exit;
    }
}
