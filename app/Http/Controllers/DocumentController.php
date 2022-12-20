<?php

namespace App\Http\Controllers;

use App\AttachFile;
use App\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $get = Document::get();
            $data['data']    = $get;
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Gagal, Mohon Reload Page';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        //
    }

    public function listDoc(Request $request, $project, $id) {
        try {
            $order = 'asc';
            if($request->get('order')) {
                $order = $request->get('order');
            }
            if ($project == 'project') {
                $model = Document::where('project_id', $id);
            } else if ($project == 'content') {
                $model = AttachFile::where('com_id', $id);
            } else {
                $model = AttachFile::where('implementation_id', $id)->where('tipe', $project);
            }

            if($request->get('sort')) {
                $model->orderBy($request->get('sort'), $order);
            } else {
                $model->orderBy('created_at', $order);
            }
            if($request->get('search')) {
                $model->where('nama', 'like','%'.$request->get('search').'%');
            }

            $data = $model->get();

            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data
            ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $e,
                'id'      =>  $id,
                'pro'      =>  $project
            ],200);
        }
    }
}
