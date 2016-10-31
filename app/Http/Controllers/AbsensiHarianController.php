<?php

namespace app\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Datatables;
use Carbon\Carbon;
use App\Karyawan;
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
        $absensi_harians = Karyawan::select(['absensi_harians.created_at', 'karyawans.id', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_keluar', 'absensi_harians.jam_lembur', 'absensi_harians.status'])
        ->leftjoin('absensi_harians', 'karyawans.id', '=', 'absensi_harians.karyawan_id')
        ->where('status_karyawan_id', '=', 2);

        // $absensi_harians = DB::table('karyawans')
        // ->select(['absensi_harians.created_at', 'karyawans.id', 'karyawans.nik', 'karyawans.nama', 'absensi_harians.jam_masuk', 'absensi_harians.jam_keluar', 'absensi_harians.jam_lembur', 'absensi_harians.status'])
        // ->leftjoin('absensi_harians', 'karyawans.id', '=', 'absensi_harians.karyawan_id')
        // ->where('status_karyawan_id', '=', 2);

        return Datatables::of($absensi_harians)
        ->editColumn('status', function ($absensi_harian) {
                return $absensi_harian->status == '1' ? 'Approved'  : 'Not Approved';
        })
        ->editColumn('created_at', function ($absensi_harian) {
            return $absensi_harian->created_at ? with(new Carbon($absensi_harian->created_at))->format('d-m-Y') : '';
        })

        ->editColumn('action', '<div class="text-center btn-group btn-group-justified"><a href="javascript:;" onClick="absensiHarianModule.showDetail({{ $id }});"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a></div>')

        ->make(true);
    }

    public function postUpload(Request $request)
    {

        // Contoh dari shopass

        /* Initial */
        $file = $request->file;

        /* Kita gunakan database transaction.
         * Dengan demikian, jika ada yang salah di sisi Database maka akan rollback
         */
        DB::beginTransaction();

        try {
            /* Master */
            Excel::selectSheetsByIndex(0)->load($file, function ($rows) {
                /* Kita hanya baca kolom ini saja */
                $rows = $rows->get(array('brand_code', 'model', 'supplier_barcode', 'size', 'color', 'price'));

                /* Untuk Setiap Baris Kita Looping (Exclude Header) */
                $rows->each(function ($row) {

                    /* Ini cara ambil value cell nya */
                    $brand_code = strval($row->brand_code);
                    $model = strtoupper(strval($row->model));
                    $size = strval($row->size);
                    $color = strtoupper(strval($row->color));
                    $supplier_barcode = strtoupper(strval($row->supplier_barcode));
                    $price = $row->price;

                    /* Cek Apakah Ada Brand Tersebut */
                    $brands = Brand::where('code', '=', $brand_code)->get();

                    if ($brands->count() == 1) {
                        $brand = $brands->first();

                        /* Jika Tidak Ditemukan, Maka Buat Baru */
                        $record = new Item();
                        $record->brand_id = $brand->id;
                        $record->supplier_barcode = $supplier_barcode;
                        $record->model = $model;
                        $record->size = $size;
                        $record->color = $color;
                        $record->normal_price = $price;
                        $record->save();
                    } else {
                        /* Batalkan Semua Transaksi ke Database */
                        DB::rollBack();

                        /* Redirect untuk Kembali */
                        Flash::error('Invalid brand code.');
                    }
                });
            })->toObject();

            DB::commit();
            Flash::success('success');
        } catch (\Exception $e) {
            /* Something went wrong */
            Flash::error('Unable to save');

            DB::rollback();
        }
        /* End of DB Transaction */

        //CONTOH 2 get from excel
        // $csv = array_map('str_getcsv', file(public_path('data_barang.csv')));

                    // for ($i = 1; $i < count($csv); ++$i) {
                    //     DB::table('items')->insert([
                    //         'model' => $csv[$i][0],
                    //         'size' => $csv[$i][4],
                    //         'color' => $csv[$i][5],
                    //         'stok' => $csv[$i][3],
                    //         'normal_price' => $csv[$i][1],
                    //         'reseller_price' => $csv[$i][2],
                    //         'buy_price' => $csv[$i][6],
                    //         'created_by' => 1,
                    //         'updated_by' => 1,
                    //         'created_at' => Date('Y-m-d H:i:s'),
                    //         'updated_at' => Date('Y-m-d H:i:s'),
                    //     ]);
                    // }

            return redirect('absensi-harian');
    }
}
