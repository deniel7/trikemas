<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Tujuan;

class TujuanController extends Controller
{
    
    public function datatables() {
        $list = Tujuan::select('id', 'kota');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/tujuan/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/tujuan/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->kota . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->make(true);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tujuan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tujuan.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = Tujuan::where('kota', $request->kota)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Tujuan pengiriman dengan nama ' . $request->kota . ' sudah ada.');
            return redirect('/tujuan/create')->withInput();
        }
        else {
            try {
                $tujuan = new Tujuan;
                $tujuan->kota = $request->kota;
                $tujuan->created_by = Auth::check() ? Auth::user()->username : '';
                $tujuan->save();
                
                return redirect('/tujuan');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/tujuan/create')->withInput();
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
        $data['tujuan'] = Tujuan::find($id);
            
        return view('tujuan.edit', $data);
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
        $count = Tujuan::where('kota', $request->kota)->count();
        $tujuan = Tujuan::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $tujuan->kota == $request->kota)) {
            try {
                $tujuan->kota = $request->kota;
                $tujuan->updated_by = Auth::check() ? Auth::user()->username : '';
                $tujuan->save();
                
                return redirect('/tujuan');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/tujuan/' . $id . '/edit')->withInput();
            }
        }
        else {
            Flash::error('Error: Tujuan pengiriman dengan nama ' . $request->kota . ' sudah ada.');
            return redirect('/tujuan/' . $id . '/edit')->withInput();
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
        $tujuan = Tujuan::find($id);
        
        try {
            $tujuan->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
