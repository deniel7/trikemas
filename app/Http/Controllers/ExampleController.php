<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables;

use App\Example;

class ExampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('example.index');
    }

    /**
     * Return datatables data
     *
     * @return Response
     */
    public function datatable() {
        $examples = Example::select(['id','name','description','created_at','updated_at']);
        return Datatables::of($examples)
        ->addColumn('action', function ($brand) {
            return '<a href="brand/'.$brand->id.'/sample-action">Sample Action</a>';
        })
        ->editColumn('created_at', '{{ $created_at->format("d F Y H:i") }}')
        ->make(true);
    }
}
