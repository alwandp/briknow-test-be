<?php

namespace App\Http\Controllers;

use App\Lesson_learned;
use App\Project;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class MyLessonLearnedController extends Controller
{
    public function index(Request $request){
        if (Auth::User()->role == 0) {
            if (empty($request->tahap) && empty($request->direktorat) && empty($request->divisi) && empty($request->search)) {
                $query = Project::whereHas('lesson_learned', function ($q) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('flag_mcs', 5);
                })->orderBy('created_at', 'DESC')->get();
            }
            if (!empty($request->tahap)) {
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('tahap', '=', $tahp);
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
	    if (!empty($request->direktorat)){
                $dir = $request->direktorat;
                $query = Project::whereHas('divisi', function ($q) use ($dir) {
                    $q->where('user_maker', Auth::user()->personal_number);
		    $q->where('direktorat', '=', $dir);
                    $q->where('flag_mcs', 5);
                })->orderBy('created_at', 'DESC')->get();
            }
            if (!empty($request->divisi)) {
                $div = $request->divisi;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('divisi_id', '=', $div);
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
            if (!empty($request->search)) {
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($key) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('nama', 'LIKE' ,'%'.$key.'%');
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
        }elseif (Auth::User()->role == 3) {
            if (empty($request->tahap) && empty($request->direktorat) && empty($request->divisi) && empty($request->search)) {
                $query = Project::whereHas('lesson_learned', function ($q) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
            if (!empty($request->tahap)) {
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('tahap', '=', $tahp);
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
	    if (!empty($request->direktorat)){
                $dir = $request->direktorat;
                $query = Project::whereHas('divisi', function ($q) use ($dir) {
                    $q->where('user_maker', Auth::user()->personal_number);
		    $q->where('direktorat', '=', $dir);
                    $q->where('flag_mcs', 5);
                })->orderBy('created_at', 'DESC')->get();
            }
            if (!empty($request->divisi)) {
                $div = $request->divisi;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('divisi_id', '=', $div);
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
            if (!empty($request->search)) {
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($key) {
                    $q->where('user_maker', Auth::user()->personal_number);
                    $q->where('nama', 'LIKE' ,'%'.$key.'%');
                    $q->where('flag_mcs', 5);
                })
                ->orderBy('created_at', 'DESC')->get();
            }
        }

        try {
            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $query
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'GET Gagal',
                'status'    =>  0,
                'data'      =>  $th
            ],200);
        }
    }

    public function getLessonLearnedPublic(Request $request){
        $validator = Validator::make($request->all(), [
            'tahap' => 'nullable',
            'divisi' => 'nullable',
            'search' => 'nullable'
        ]);

        if (Auth::User()->role == 0) {
             $temp = [3,4,5,6];
            if (empty($request->tahap) && empty($request->direktorat) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])
                    ->where('flag_mcs', 5)
                    ->get();
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
        }else{
            if (empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])
                    ->where('flag_mcs', 5)
                    ->get();
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
        }

        // $asc = Project::all()->orderBy('nama', 'ASC')->get();

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $query
        ],200);
    }
}
