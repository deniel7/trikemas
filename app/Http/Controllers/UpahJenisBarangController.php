<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Datatables;
use App\Report_Jenis;

class UpahJenisBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('upah-jenis-barang.index');
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

            $html .= '<a href="upah-jenis-barang/'.$upah_jenis_barang->id.'/edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a>';

            $html .= '<a href="javascript:;" onclick="upahJenisBarangModule.confirmDelete(event, \''.$upah_jenis_barang->id.'\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';

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
    public function edit($karyawan)
    {
        $data['status_karyawans'] = StatusKaryawan::select('id', 'keterangan')->orderBy('id')->get();

        return view('karyawan_staff/edit', compact('karyawan'), $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Karyawan $karyawan, Request $request)
    {
        $nilai_upah = str_replace(',', '', $request->input('nilai_upah'));
        $uang_makan = str_replace(',', '', $request->input('uang_makan'));
        $tunjangan_jabatan = str_replace(',', '', $request->input('tunjangan_jabatan'));
        $pot_koperasi = str_replace(',', '', $request->input('pot_koperasi'));
        $pot_bpjs = str_replace(',', '', $request->input('pot_bpjs'));

        $karyawan->nilai_upah = $nilai_upah;
        $karyawan->uang_makan = $uang_makan;
        $karyawan->tunjangan = $tunjangan_jabatan;
        $karyawan->pot_koperasi = $pot_koperasi;
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

        return redirect('karyawan-staff');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($karyawan)
    {
        DB::beginTransaction();

        try {
            $karyawan->delete();

            DB::commit();
            echo 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            echo 'Error ('.$e->errorInfo[1].'): '.$e->errorInfo[2].'.';
        }
    }
}
