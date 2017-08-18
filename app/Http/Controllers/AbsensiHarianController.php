<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Datatables;
use Carbon\Carbon;
use App\AbsensiHarian;
use Illuminate\Http\Request;
use Flash;
use App\Karyawan;
use DB;

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

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    // public function show()
    // {
    //     return redirect('absensi-harian');
    // }

    public function datatable()
    {
        $absensi_harians = AbsensiHarian::select(['absensi_harians.id as id_absen', 'absensi_harians.tanggal', 'absensi_harians.karyawan_id', 'absensi_harians.jam_masuk', 'absensi_harians.jam_pulang', 'absensi_harians.jam_lembur', 'absensi_harians.jam_kerja', 'absensi_harians.scan_masuk', 'absensi_harians.scan_pulang', 'absensi_harians.terlambat', 'absensi_harians.plg_cepat', 'absensi_harians.jml_jam_kerja', 'absensi_harians.departemen', 'absensi_harians.jml_kehadiran', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.konfirmasi_lembur', 'absensi_harians.jenis_lembur', 'absensi_harians.status'])
        ->leftjoin('karyawans', 'karyawans.nik', '=', 'absensi_harians.karyawan_id')
        ->orderby('absensi_harians.id');

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
            getAbsenExcel($file, 3);

            Flash::success('success');
        } else {
            Flash::error('File karyawan STAFF belum dipilih');
        }

        if (!empty($file2)) {
            getAbsenExcel($file2, 1);
            Flash::success('success');
        } else {
            Flash::error('File karyawan KONTRAK belum dipilih');
        }

        if (!empty($file3)) {
            getAbsenExcel($file3, 2);
            Flash::success('success');
        } else {
            Flash::error('File karyawan HARIAN belum dipilih');
        }

        return redirect('absensi-harian');
    }

    public function postLembur(Request $request)
    {
        // $file = $request->file('file');

        // if (!empty($file)) {
        //     getLemburExcel($file);
        //     Flash::success('Absen Karyawan Lembur berhasil ditambahkan');
        // } else {
        //     Flash::error('File karyawan Lembur belum dipilih');
        // }
        $jam = $request->input('jam');

        $record = new AbsensiHarian();

        $record->tanggal = $request->input('tgl_lembur');
        $record->karyawan_id = $request->input('karyawan');
        $record->jenis_lembur = $request->input('jenis_lembur');
        $record->jam_kerja = 'LEMBUR';

        $record->jam = $jam;
        $record->menit = 0;

        $record->jam_lembur = $jam.':00:00';

        $record->save();

        DB::commit();
        Flash::success('Absensi Lembur Berhasil Disimpan');

        return redirect('absensi-harian');
    }

    public function getTambahAbsensi()
    {

        // $products = Product::select('id', 'article_code', 'brand', 'product_name', 'status')->distinct('article_code');

        $data['nik'] = 0;

        // /* Jika Ada Family ID */
        // if (! is_null($request->input('family_id')) && $request->input('family_id') != 'ANY') {
        //     $products = $products->where('family_id', $request->input('family_id'));
        //     $data['family_id'] = $request->input('family_id');
        // } else {
        //     $products = $products->where('brand', 'XYZ');
        // }

        // /* Jika Ada Brand */
        // if (! is_null($request->input('brand')) && $request->input('brand') != 'ANY') {
        //     $products = $products->where('brand', $request->input('brand'));
        //     $data['brand'] = $request->input('brand');
        // }

        // $products = $products->orderBy('status', 'DESC')
        // ->orderBy('article_code', 'ASC')
        // ->get();

        // $data['products'] = $products;
        $data['karyawans'] = Karyawan::all();
        //$data['brand'] = '-';

        return view('absensi-harian.tambah-absen', $data);
    }

    public function categoryDropDownData()
    {
        $subcategories = DB::table('karyawans')
                // ->join('natural_accounts', 'reduction_items.natural_account_id', '=', 'natural_accounts.code')
                // ->join('cost_centers', 'reduction_items.cost_center_id', '=', 'cost_centers.code')
                // ->join('products', 'reduction_items.product_id', '=', 'products.code')
                // ->select('reduction_items.*', 'natural_accounts.description as nat_account_desc', 'cost_centers.description as cost_center_desc', 'products.description as product_desc')
                // ->where('reduction_items.id', '=', $cat_id)
                ->get();

        return Response::json($subcategories);
    }
}
