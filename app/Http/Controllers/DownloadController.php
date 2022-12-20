<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use stdClass;

class DownloadController extends Controller
{
    public $successStatus = 200;

    public function searchproject(){
        $model = Project::orderBy('nama', 'asc')->limit(8);
        $cari = request()->searchTerm;
        $impl = request()->impl;
        $no_pub = request()->nonpublic;
        if ($cari != null) {
            $model->where('nama', 'like', "%$cari%");
        }

        if (!$no_pub && !$no_pub == null && $no_pub != 'Y') {
            $model->where('flag_mcs',5);
        }
        $hasil = $model->get();

        $data = [];
        foreach ($hasil as $key) {
            $data[]         = array("id"=>$key->id, "text"=>$key->nama, "image"=>$key->thumbnail, "disabled" => ($impl == 'Y' && sizeof($key->implementation) > 0));
        }

        return json_encode($data);
    }

    public function detailproject($id){
        try {
            $result = Project::with(['user_restrict'])->where('id',$id)->with('consultant','divisi','project_managers','document')->where('flag_mcs',5)->first();
            return response()->json([
                "status"  =>  1,
                "data"    =>  $result
            ],200);
        } catch (\Throwable $th) {
            $result['message']  =   "Something Error";
            return response()->json([
                "status"  =>  0,
                "data"    =>  $result
            ],200);
        }
    }
}
