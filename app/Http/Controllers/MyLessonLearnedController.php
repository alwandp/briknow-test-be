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
        $lessonLearn = Project::with(['lesson_learned'])->join('divisis', 'projects.divisi_id', '=', 'divisis.id')->where('flag_mcs', 5)
            ->select(DB::raw('projects.id as id, nama, slug, flag_mcs, projects.created_at, projects.updated_at, divisi_id, divisis.direktorat'))->orderBy('updated_at', 'DESC');

        if (!isset($request->tahap) && !isset($request->direktorat) && !isset($request->divisi) && !isset($request->search)) {
            $query = $lessonLearn;
        }
        if (isset($request->tahap)){
            $tahp = $request->tahap;
            $query = $lessonLearn->whereHas('lesson_learned', function ($q) use ($tahp) {
                $q->where('tahap', '=', $tahp);
            });
        }
        if (isset($request->direktorat)){
            $dir = $request->direktorat;
            if ($dir !== "init") {
                $query = $lessonLearn->where('direktorat', $dir);
            } else {
                $query = $lessonLearn->where('direktorat', 0);
            }
        }
        if (isset($request->divisi)){
            $div = $request->divisi;
            if ($div !== "init") {
                $query = $lessonLearn->where('divisi_id', $div);
            } else {
                $query = $lessonLearn;
            }
        }
        if(isset($request->search)){
            $key = $request->search;
            $query = $lessonLearn->where('nama', 'LIKE', '%'.$key.'%');
        }
        // $query = Project::with(['lesson_learned'])->where('flag_mcs', 5);
        $data = $query->paginate(10);
        $count = count($data);

        $paginate   = view('lesson_learned.paginate',compact('data'))->render();
        $data['paginate'] = $paginate;
        $data['total'] = $count;
        // $asc = Project::all()->orderBy('nama', 'ASC')->get();
        // dd($data);

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $data
        ],200);
    }
}
