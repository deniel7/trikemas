<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Konsumen;

class KonsumenController extends Controller
{
    
    public function datatables() {
        $list = Konsumen::select('id', 'nama', 'alamat', 'hp');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/konsumen/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/konsumen/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->nama . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->make(true);
    }
    
    public function branch($id) {
        $konsumen_branch = Konsumen::find($id)->branch;
        
        //$opt = '<option value="">-- Pilih konsumen branch --</option>';
        $opt = '';
        foreach($konsumen_branch as $branch) {
            $opt .= '<option value="' . $branch->id . '">' . $branch->nama . '</option>';
        }
        
        echo $opt;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('konsumen.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('konsumen.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = Konsumen::where('nama', $request->nama)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Konsumen dengan nama ' . $request->nama . ' sudah ada.');
            return redirect('/konsumen/create')->withInput();
        }
        else {
            try {
                $konsumen = new Konsumen;
                $konsumen->nama = $request->nama;
                $konsumen->alamat = $request->alamat;
                $konsumen->hp = $request->hp;
                $konsumen->created_by = Auth::check() ? Auth::user()->username : '';
                $konsumen->save();
                
                return redirect('/konsumen');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/konsumen/create')->withInput();
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
        $data['konsumen'] = Konsumen::find($id);
        
        return view('konsumen.edit', $data);
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
        $count = Konsumen::where('nama', $request->nama)->count();
        $konsumen = Konsumen::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $konsumen->nama == $request->nama)) {
            try {
                $konsumen->nama = $request->nama;
                $konsumen->alamat = $request->alamat;
                $konsumen->hp = $request->hp;
                $konsumen->updated_by = Auth::check() ? Auth::user()->username : '';
                $konsumen->save();
                
                return redirect('/konsumen');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/konsumen/' . $id . '/edit')->withInput();
            }
        }
        else {
            Flash::error('Error: Konsumen dengan nama ' . $request->nama . ' sudah ada.');
            return redirect('/konsumen/' . $id . '/edit')->withInput();
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
        $konsumen = Konsumen::find($id);
        
        try {
            $konsumen->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
