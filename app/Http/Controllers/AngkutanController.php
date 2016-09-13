<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Angkutan;

class AngkutanController extends Controller
{
    
    public function datatables() {
        $list = Angkutan::select('id', 'nama');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/angkutan/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/angkutan/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->nama . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
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
        return view('angkutan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('angkutan.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = Angkutan::where('nama', $request->nama)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Angkutan dengan nama ' . $request->nama . ' sudah ada.');
            return redirect('/angkutan/create')->withInput();
        }
        else {
            try {
                $angkutan = new Angkutan;
                $angkutan->nama = $request->nama;
                $angkutan->created_by = Auth::check() ? Auth::user()->username : '';
                $angkutan->save();
                
                return redirect('/angkutan');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/angkutan/create')->withInput();
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
        $data['angkutan'] = Angkutan::find($id);
        
        return view('angkutan.edit', $data);
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
        $count = Angkutan::where('nama', $request->nama)->count();
        $angkutan = Angkutan::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $angkutan->nama == $request->nama)) {
            try {
                $angkutan->nama = $request->nama;
                $angkutan->updated_by = Auth::check() ? Auth::user()->username : '';
                $angkutan->save();
                
                return redirect('/angkutan');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/angkutan/' . $id . '/edit')->withInput();
            }
        }
        else {
            Flash::error('Error: Angkutan dengan nama ' . $request->nama . ' sudah ada.');
            return redirect('/angkutan/' . $id . '/edit')->withInput();
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
        $angkutan = Angkutan::find($id);
        
        try {
            $angkutan->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
