<?php

namespace App\Http\Controllers;

use App\Lesson_learned;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;

class LessonLearnedController extends Controller
{
    public function getAll(Request $request){
            $validator = Validator::make($request-> all(), [
                'tahap' => 'nullable',
                'divisi' => 'nullable',
                'search' => 'nullable'
            ]);

            if (empty($request->tahap) && empty($request->direktorat) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])->where('flag_mcs', 5)->get();
            }
	    if (!empty($request->tahap)){
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('tahap', '=', $tahp);
		    $q->where('flag_mcs', 5);
                })->get();
            }
	    if (!empty($request->direktorat)){
                $dir = $request->direktorat;
                $query = Project::whereHas('divisi', function ($q) use ($dir) {
                    $q->where('direktorat', '=', $dir);
		    $q->where('flag_mcs', 5);
                })->get();
            }
	    if (!empty($request->divisi)){
                $div = $request->divisi;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div) {
                    $q->where('divisi_id', '=', $div);
		    $q->where('flag_mcs', 5);
                })->get();
            }
	    if(!empty($request->search)){
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ( $key) {
                    $q->where('nama', 'LIKE', '%'.$key.'%');
		    $q->where('flag_mcs', 5);
                })->get();
            }

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $query
            ],200);
    }
}
