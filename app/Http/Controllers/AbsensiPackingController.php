<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use Datatables;
use Carbon\Carbon;
use DB;
use Validator;
use App\Http\Controllers\Controller;
use App\AbsensiPacking;
use App\NaturalAccount;
use App\CostCenter;
use App\Product;

class AbsensiPackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['header_id'] = $request->segment(2);

        return view('absensi-packing.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [];
        // $data['natural_accounts'] = NaturalAccount::all();
        // $data['cost_centers'] = CostCenter::all();
        // $data['products'] = Product::all();

        // $data['natural_account_id'] = $request->input('natural_account_id');

        // $data['cost_center_id'] = $request->input('cost_center_id');

        // $data['product_id'] = $request->input('product_id');

        return view('absensi-packing.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* RBAC */
        // if (!\App\User::authorize('reduction_item.create')) {
        //     return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        // }
        //dd($request->input);
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'tgl' => 'required',
                'bagian' => 'required',
                'jenis' => 'required',
                'quantity' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => 'Terdapat Data yang belum diisi.'));
            } else {
                $tgl = $request->input('tgl');
                $bagian = $request->input('bagian');
                $jenis = $request->input('jenis');
                $jumlah = $request->input('quantity');

                AbsensiPacking::create([
                    'tanggal' => $tgl,
                    'bagian' => $bagian,
                    'jenis' => $jenis,
                    'jumlah' => $jumlah,

                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Input Absensi Packing Berhasil'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /* RBAC */
        if (!\App\User::authorize('reduction_item.show')) {
            flash('Insufficient permission', 'warning');

            return redirect('home');
        }

        $data['reduction_item'] = ReductionItem::findOrFail($id);
        $data['natural_accounts'] = NaturalAccount::all();
        $data['cost_centers'] = CostCenter::all();
        $data['products'] = Product::all();

        return view('reduction_item.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* RBAC */
        if (!\App\User::authorize('reduction_item.edit')) {
            flash('Insufficient permission', 'warning');

            return redirect('home');
        }

        try {
            $data = [];
            $data['natural_accounts'] = NaturalAccount::all();
            $data['cost_centers'] = CostCenter::all();
            $data['products'] = Product::all();

            $data['reduction_item'] = ReductionItem::findOrFail($id);

            return view('reduction_item.edit', $data);
        } catch (Exception $e) {
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /* RBAC */
        if (!\App\User::authorize('reduction_item.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'natural_account_id' => 'required',
                'cost_center_id' => 'required',
                'product_id' => 'required',
                'flag' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => 'Please fill all the required fields.'));
            } else {
                $name = $request->input('name');
                $natural_account_id = $request->input('natural_account_id');
                $cost_center_id = $request->input('cost_center_id');
                $product_id = $request->input('product_id');
                $flag = $request->input('flag');

                ReductionItem::findOrFail($id)->update([
                    'name' => $name,
                    'natural_account_id' => $natural_account_id,
                    'cost_center_id' => $cost_center_id,
                    'product_id' => $product_id,
                    'flag' => $flag,
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated ReductionItem.'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* RBAC */
        if (!\App\User::authorize('reduction_item.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = ReductionItem::findOrFail($id);
            $post->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted ReductionItem.'));
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatable($header_id = null)
    {
        $absensi_packings = AbsensiPacking::select(['id', 'tanggal', 'bagian', 'jenis', 'jumlah']);

        return Datatables::of($absensi_packings)
        ->addColumn('action', function ($absensi_packing) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $url = 'reduction-item/'.$absensi_packing->id;
                $buttons .= '<li><a href="/'.$url.'">View</a></li>';
                $buttons .= '<li><a href="/'.$url.'/edit">Edit</a></li>';

                $buttons .= '<li class="divider"></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="'.$absensi_packing->id.'" onclick="ReductionItemModule.deleteReductionItem($(this));">Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
        })
        ->editColumn('tanggal', function ($absensi_packing) {
                return $absensi_packing->tanggal ? with(new Carbon($absensi_packing->tanggal))->format('d F Y') : '';
        })

        ->make(true);
    }
}
