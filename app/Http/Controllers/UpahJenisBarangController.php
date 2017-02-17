<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Datatables;
use App\Report_Jenis;
use Flash;

class UpahJenisBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (in_array(200, session()->get('allowed_menus'))) {
            return view('upah-jenis-barang.index');
        } else {
            //
        }
    }

    public function datatable()
    {
        $upah_jenis_barangs = DB::table('report_jenis')
        ->select(['id', 'nama', 'upah']);

        return Datatables::of($upah_jenis_barangs)

        ->editColumn('nama', '<span class="pull-right">{{ $nama }}</span>')
        ->editColumn('upah', '<span class="pull-right">{{ $upah }}</span>')

        ->addColumn('action', function ($upah_jenis_barang) {

            $html = '<div class="text-center btn-group btn-group-justified">';
            if (in_array(202, session()->get('allowed_menus'))) {
                $html .= '<a href="upah-jenis-barang/edit/'.$upah_jenis_barang->id.'"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a>';
            }
            if (in_array(203, session()->get('allowed_menus'))) {
                $html .= '<a href="javascript:;" onclick="upahJenisBarangModule.confirmDelete(event, \''.$upah_jenis_barang->id.'\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
            }
            $html .= '</div>';

            return $html;
        })
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $data['upah_jenis_barangs'] = StatusKaryawan::select('id', 'keterangan')->orderBy('id')->get();

        return view('upah-jenis-barang/create');
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
        $nama = str_replace(',', '', $request->input('nama'));

        $upah = str_replace(',', '', $request->input('upah'));

        $upah_jenis_barang = new Report_Jenis();
        $upah_jenis_barang->nama = $nama;
        $upah_jenis_barang->upah = $upah;

        $upah_jenis_barang->save();

        DB::commit();

        Flash::success('Saved');

        return redirect('upah-jenis-barang');
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
        $id = $id->id;

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    // public function edit($karyawan)
    // {
    //     if (in_array(202, session()->get('allowed_menus'))) {
    //         $data['status_karyawans'] = Report_Jenis::select('id', 'keterangan')->orderBy('id')->get();

    //         return view('upah-jenis-barang/edit', compact('karyawan'), $data);
    //     } else {
    //         //
    //     }
    // }

    public function editUpah($id)
    {
        if (in_array(202, session()->get('allowed_menus'))) {
            //$data['jenis_upah'] = Report_Jenis::select('id', 'nama', 'upah')->orderBy('id')->get();

            $jenis_upah = Report_Jenis::find($id);
            $data['jenis_upah'] = $jenis_upah;

            return view('upah-jenis-barang/edit', $data);
        } else {
            //
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
    public function updateUpah($id, Request $request)
    {
        $nama = str_replace(',', '', $request->input('nama'));
        $upah = str_replace(',', '', $request->input('upah'));

        $jenis_upah = Report_Jenis::find($id);
        $jenis_upah->nama = $nama;
        $jenis_upah->upah = $upah;
        $jenis_upah->save();
        DB::commit();

        return redirect('upah-jenis-barang');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($report_jenis)
    {
        DB::beginTransaction();

        try {
            $report_jenis->delete();

            DB::commit();
            echo 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            echo 'Error ('.$e->errorInfo[1].'): '.$e->errorInfo[2].'.';
        }
    }

    public function deleteUpah($id)
    {
        $upah = Report_Jenis::find($id);

        DB::beginTransaction();
        try {
            // delete detail
            Report_Jenis::where('id', $id)->delete();

            // delete header
            $upah->delete();
            DB::commit();
            echo 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            echo 'Error ('.$e->errorInfo[1].'): '.$e->errorInfo[2].'.';
        }
    }
}
