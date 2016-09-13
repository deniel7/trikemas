<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Angkutan;
use App\Tujuan;
use App\AngkutanTujuan;

class AngkutanTujuanController extends Controller
{
    
    public function datatables() {
        $list = AngkutanTujuan::select('id', 'angkutan_id', 'tujuan_id', 'harga');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $angkutan = Angkutan::find($list->angkutan_id);
                    $tujuan = Tujuan::find($list->tujuan_id);
                    
                    $nama_angkutan = $angkutan ? $angkutan->nama : '';
                    $nama_tujuan = $tujuan ? $tujuan->kota : '';
                    
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/angkutan-tujuan/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/angkutan-tujuan/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $nama_angkutan . '\', \'' . $nama_tujuan . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->editColumn('angkutan_id', function($list) {
                    $angkutan = Angkutan::find($list->angkutan_id);
                    return $angkutan ? $angkutan->nama : '';
                })
                ->editColumn('tujuan_id', function($list) {
                    $tujuan = Tujuan::find($list->tujuan_id);
                    return $tujuan ? $tujuan->kota : '';
                })
                ->editColumn('harga', '{{ number_format($harga, "2", ".", ",") }}')
                ->make(true);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('angkutan_tujuan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
        $data['tujuan'] = Tujuan::select('id', 'kota as nama')->orderBy('nama')->get();
        
        return view('angkutan_tujuan.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = AngkutanTujuan::where('angkutan_id', $request->angkutan_id)->where('tujuan_id', $request->tujuan_id)->count();
        
        // check exist
        if ($count > 0) {
            $angkutan = Angkutan::find($request->angkutan_id);
            $tujuan = Tujuan::find($request->tujuan_id);
            $nama_angkutan = $angkutan ? $angkutan->nama : '';
            $nama_tujuan = $tujuan ? $tujuan->kota : '';
            Flash::error('Error: Angkutan dengan nama ' . $nama_angkutan . ' dan tujuan ' . $nama_tujuan . ' sudah ada.');
            return redirect('/angkutan-tujuan/create')->withInput();
        }
        else {
            try {
                $angkutan_tujuan = new AngkutanTujuan;
                $angkutan_tujuan->angkutan_id = $request->angkutan_id;
                $angkutan_tujuan->tujuan_id = $request->tujuan_id;
                $angkutan_tujuan->harga = str_replace(',', '', $request->harga);
                $angkutan_tujuan->created_by = Auth::check() ? Auth::user()->username : '';
                $angkutan_tujuan->save();
                
                return redirect('/angkutan-tujuan');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/angkutan-tujuan/create')->withInput();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
        $data['tujuan'] = Tujuan::select('id', 'kota as nama')->orderBy('nama')->get();
        $data['angkutan_tujuan'] = AngkutanTujuan::find($id);
        
        return view('angkutan_tujuan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $count = AngkutanTujuan::where('angkutan_id', $request->angkutan_id)->where('tujuan_id', $request->tujuan_id)->count();
        $angkutan_tujuan = AngkutanTujuan::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $angkutan_tujuan->angkutan_id == $request->angkutan_id && $angkutan_tujuan->tujuan_id == $request->tujuan_id)) {
            try {
                $angkutan_tujuan->angkutan_id = $request->angkutan_id;
                $angkutan_tujuan->tujuan_id = $request->tujuan_id;
                $angkutan_tujuan->harga = str_replace(',', '', $request->harga);
                $angkutan_tujuan->updated_by = Auth::check() ? Auth::user()->username : '';
                $angkutan_tujuan->save();
                
                return redirect('/angkutan-tujuan');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/angkutan-tujuan/' . $id . '/edit')->withInput();
            }
        }
        else {
            $angkutan = Angkutan::find($request->angkutan_id);
            $tujuan = Tujuan::find($request->tujuan_id);
            $nama_angkutan = $angkutan ? $angkutan->nama : '';
            $nama_tujuan = $tujuan ? $tujuan->kota : '';
            Flash::error('Error: Angkutan dengan nama ' . $nama_angkutan . ' dan tujuan ' . $nama_tujuan. ' sudah ada.');
            return redirect('/angkutan-tujuan/' . $id . '/edit')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $angkutan_tujuan = AngkutanTujuan::find($id);
        
        try {
            $angkutan_tujuan->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
