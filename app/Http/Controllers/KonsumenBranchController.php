<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Konsumen;
use App\KonsumenBranch;
use DB;

class KonsumenBranchController extends Controller
{
    
    public function datatables() {
        $list = DB::table('konsumen_branches')
                ->leftJoin('konsumens', 'konsumen_branches.konsumen_id', '=', 'konsumens.id')
                ->select('konsumen_branches.id', 'konsumen_branches.nama', 'konsumen_branches.alamat', 'konsumen_branches.hp', 'konsumen_branches.konsumen_id', 'konsumens.nama as nama_konsumen');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/konsumen-branch/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/konsumen-branch/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->nama . '\', \'' . $list->nama_konsumen . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
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
        return view('konsumen_branch.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        
        return view('konsumen_branch.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = KonsumenBranch::where('nama', $request->nama)->where('konsumen_id', $request->konsumen_id)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Konsumen branch dengan nama ' . $request->nama . ' pada konsumen ' . Konsumen::find($request->konsumen_id)->nama . ' sudah ada.');
            return redirect('/konsumen-branch/create')->withInput();
        }
        else {
            try {
                $konsumen_branch = new KonsumenBranch;
                $konsumen_branch->nama = $request->nama;
                $konsumen_branch->alamat = $request->alamat;
                $konsumen_branch->hp = $request->hp;
                $konsumen_branch->konsumen_id = $request->konsumen_id;
                $konsumen_branch->created_by = Auth::check() ? Auth::user()->username : '';
                $konsumen_branch->save();
                
                return redirect('/konsumen-branch');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/konsumen-branch/create')->withInput();
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
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        $data['konsumen_branch'] = KonsumenBranch::find($id);
        
        return view('konsumen_branch.edit', $data);
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
        $count = KonsumenBranch::where('nama', $request->nama)->where('konsumen_id', $request->konsumen_id)->count();
        $konsumen_branch = KonsumenBranch::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $konsumen_branch->nama == $request->nama)) {
            try {
                $konsumen_branch->nama = $request->nama;
                $konsumen_branch->alamat = $request->alamat;
                $konsumen_branch->hp = $request->hp;
                $konsumen_branch->konsumen_id = $request->konsumen_id;
                $konsumen_branch->updated_by = Auth::check() ? Auth::user()->username : '';
                $konsumen_branch->save();
                
                return redirect('/konsumen-branch');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/konsumen-branch/' . $id . '/edit')->withInput();
            }
        }
        else {
            Flash::error('Error: Konsumen branch dengan nama ' . $request->nama . ' pada konsumen ' . Konsumen::find($request->konsumen_id)->nama . ' sudah ada.');
            return redirect('/konsumen-branch/' . $id . '/edit')->withInput();
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
        $konsumen_branch = KonsumenBranch::find($id);
        
        try {
            $konsumen_branch->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
