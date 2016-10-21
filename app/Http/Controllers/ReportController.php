<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Carbon\Carbon;
use PDF;
use App\InvoicePenjualan;
use App\DetailPenjualan;

class ReportController extends Controller
{
    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        //
    }

    private function readMonth($month) {
        $monthLbl = "";
        switch($month) {
            case 1:
                $monthLbl = "Januari";
                break;
            case 2:
                $monthLbl = "Februari";
                break;
            case 3:
                $monthLbl = "Maret";
                break;
            case 4:
                $monthLbl = "April";
                break;
            case 5:
                $monthLbl = "Mei";
                break;
            case 6:
                $monthLbl = "Juni";
                break;
            case 7:
                $monthLbl = "Juli";
                break;
            case 8:
                $monthLbl = "Agustus";
                break;
            case 9:
                $monthLbl = "September";
                break;
            case 10:
                $monthLbl = "Oktober";
                break;
            case 11:
                $monthLbl = "November";
                break;
            case 12:
                $monthLbl = "Desember";
                break;
        }
        return $monthLbl;
    }

    // prompt
    public function penjualan() {
        $data['default_date'] = date('d-m-Y');

        return view('report.penjualan_params', $data);
    }

    // preview
    public function previewPenjualan($tanggal, $hingga = "") {
        $tanggal_en = Carbon::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
        
        if ($hingga !== "") {
            $hingga_en = Carbon::createFromFormat('d-m-Y', $hingga)->format('Y-m-d');
            
            $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select('invoice_penjualans.id', 'invoice_penjualans.tanggal', 'invoice_penjualans.no_invoice', 'invoice_penjualans.no_po', 'invoice_penjualans.no_surat_jalan',
                             'invoice_penjualans.no_mobil', 'invoice_penjualans.tgl_jatuh_tempo', 'invoice_penjualans.bank_tujuan_bayar', 'invoice_penjualans.tanggal_bayar',
                             'invoice_penjualans.status_bayar', 'invoice_penjualans.keterangan',
                             'invoice_penjualans.sub_total', 'invoice_penjualans.diskon', 'invoice_penjualans.total', 'invoice_penjualans.ppn', 'invoice_penjualans.grand_total', 
                             'detail_penjualans.jumlah_ball', 'detail_penjualans.jumlah as jumlah_pcs', 'detail_penjualans.harga_barang', 'detail_penjualans.subtotal', 
                             'angkutans.nama as nama_angkutan', 'tujuans.kota as nama_tujuan', 'konsumens.nama as nama_konsumen',
                             'barangs.jenis as jenis_barang', 'barangs.nama as nama_barang', 'barangs.berat as berat_barang')
                    ->whereBetween('invoice_penjualans.tanggal', [$tanggal_en, $hingga_en])
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
        }
        else {
            $data = DB::table('invoice_penjualans')
                    ->join('detail_penjualans', 'detail_penjualans.invoice_penjualan_id', '=', 'invoice_penjualans.id')
                    ->join('barangs', 'barangs.id', '=', 'detail_penjualans.barang_id')
                    ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                    ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                    ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                    ->select('invoice_penjualans.id', 'invoice_penjualans.tanggal', 'invoice_penjualans.no_invoice', 'invoice_penjualans.no_po', 'invoice_penjualans.no_surat_jalan',
                             'invoice_penjualans.no_mobil', 'invoice_penjualans.tgl_jatuh_tempo', 'invoice_penjualans.bank_tujuan_bayar', 'invoice_penjualans.tanggal_bayar',
                             'invoice_penjualans.status_bayar', 'invoice_penjualans.keterangan',
                             'invoice_penjualans.sub_total', 'invoice_penjualans.diskon', 'invoice_penjualans.total', 'invoice_penjualans.ppn', 'invoice_penjualans.grand_total', 
                             'detail_penjualans.jumlah_ball', 'detail_penjualans.jumlah as jumlah_pcs', 'detail_penjualans.harga_barang', 'detail_penjualans.subtotal', 
                             'angkutans.nama as nama_angkutan', 'tujuans.kota as nama_tujuan', 'konsumens.nama as nama_konsumen',
                             'barangs.jenis as jenis_barang', 'barangs.nama as nama_barang', 'barangs.berat as berat_barang')
                    ->where('invoice_penjualans.tanggal', $tanggal_en)
                    ->orderBy('invoice_penjualans.tanggal')->orderBy('invoice_penjualans.no_invoice')->orderBy('barangs.nama')
                    ->get();
        }

        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Laporan Penjualan - Trimitra Kemasindo');
        PDF::SetSubject('Laporan Penjualan');
        PDF::SetKeywords('Laporan Penjualan Trimitra Kemasindo');

        PDF::setFooterCallback(function($pdf) {
            $pdf->SetMargins(15, 10, 15);

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 4, 'Page ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 0, 'R', 0, '', 0);
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
        PDF::Cell(50, 0, 'KONSUMEN', 1, 0, 'C', 0, '', 0);
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
            foreach($data as $item) {
                
                if ($checkInvoice != $item->no_invoice) {
                    PDF::Cell(22, 0, Carbon::createFromFormat('Y-m-d', $item->tanggal)->format('d-m-Y'), 1, 0, 'L', 0, '', 1);
                    PDF::Cell(26, 0, $item->no_invoice, 1, 0, 'L', 0, '', 1);
                    PDF::Cell(50, 0, $item->nama_konsumen, 1, 0, 'L', 0, '', 1);
                    PDF::Cell(30, 0, $item->nama_tujuan, 1, 0, 'L', 0, '', 1);    
                }
                else {
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
                }
                else {
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
        }
        else {
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

}
