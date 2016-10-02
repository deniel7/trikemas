<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Karyawan;
use App\StatusKaryawan;
use Datatables;

class KaryawanTetapController extends Controller
{
    public function index()
    {
        return view('karyawan.index');
    }

    public function datatable()
    {
        $karyawans = DB::table('karyawans')
        ->select(['karyawans.id', 'status_karyawans.keterangan', 'karyawans.nik', 'karyawans.nama', 'karyawans.alamat', 'karyawans.phone', 'karyawans.lulusan', 'karyawans.tgl_masuk', 'karyawans.nilai_upah', 'karyawans.uang_makan', 'karyawans.uang_lembur', 'karyawans.norek'])
        ->join('status_karyawans', 'karyawans.status_karyawan_id', '=', 'status_karyawans.id')
        ->where('karyawans.status_karyawan_id', '=', 1);

        return Datatables::of($karyawans)

        ->editColumn('status_karyawan_id', '<span class="pull-right">{{ App\Karyawan::find($id)->statusKaryawan->keterangan }}</span>')
        ->editColumn('nama', '<span class="pull-right">{{ $nama }}</span>')
        ->editColumn('alamat', '<span class="pull-right">{{ $alamat }}</span>')
        ->editColumn('phone', '<span class="pull-right">{{ $phone }}</span>')
        ->editColumn('lulusan', '<span class="pull-right">{{ $lulusan }}</span>')
        ->editColumn('tgl_masuk', '<span class="pull-right">{{ $tgl_masuk }}</span>')
        ->editColumn('nilai_upah', '<span class="pull-right">{{ $nilai_upah }}</span>')
        ->editColumn('uang_makan', '<span class="pull-right">{{ $uang_makan }}</span>')
        ->editColumn('uang_lembur', '<span class="pull-right">{{ $uang_lembur }}</span>')
        ->editColumn('norek', '<span class="pull-right">{{ $norek }}</span>')
        ->addColumn('action', function ($karyawan) {
            $html = '<div style="width: 70px; margin: 0px auto;" class="text-center btn-group btn-group-justified" role="group">';
            $html .= '<a role="button" class="btn btn-warning" href="karyawan-tetap/'.$karyawan->id.'/edit"><i class="fa fa-fw fa-pencil"></i> EDIT</a>';
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

        $karyawan->nilai_upah = $nilai_upah_;
        $karyawan->uang_makan = $uang_makan_;
        $karyawan->uang_lembur = $uang_lembur_;
        //dd($request);
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
}
