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
    public function index(){
        if (Auth::User()->role == 0) {
            $query = Lesson_learned::join('projects', 'projects.id', '=', 'lesson_learneds.project_id')
                ->join('divisis', 'divisis.id', '=', 'lesson_learneds.divisi_id')
                ->join('consultants', 'consultants.id', '=', 'lesson_learneds.consultant_id')
                ->where('lesson_learneds.user_maker', Auth::user()->personal_number)
                ->select(DB::raw("lesson_learneds.id, divisis.direktorat, divisis.divisi, projects.nama as project_name, consultants.nama as consultant_name,
                        lesson_learneds.tahap, lesson_learneds.lesson_learned, lesson_learneds.detail, lesson_learneds.created_at,
                        lesson_learneds.updated_at"))
                ->orderBy('lesson_learneds.created_at', 'DESC')
                ->paginate(10);
        }elseif(Auth::User()->role == 3){
            $query = Lesson_learned::join('projects', 'projects.id', '=', 'lesson_learneds.project_id')
                ->join('divisis', 'divisis.id', '=', 'lesson_learneds.divisi_id')
                ->join('consultants', 'consultants.id', '=', 'lesson_learneds.consultant_id')
                ->select(DB::raw("lesson_learneds.id, divisis.direktorat, divisis.divisi, projects.nama as project_name, consultants.nama as consultant_name,
                        lesson_learneds.tahap, lesson_learneds.lesson_learned, lesson_learneds.detail, lesson_learneds.created_at,
                        lesson_learneds.updated_at"))
                ->orderBy('lesson_learneds.created_at', 'DESC')
                ->paginate(10);
        }
        try {
            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $query;
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
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
            if (empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif (!empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('tahap', '=', $tahp);
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif (empty($request->tahap) && !empty($request->divisi) && empty($request->search)){
                $div = $request->divisi;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div) {
                    $q->where('divisi_id', '=', $div);
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif(empty($request->tahap) && empty($request->divisi) && !empty($request->search)){
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ( $key) {
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif (!empty($request->tahap) && !empty($request->divisi) && empty($request->search)){
                $div = $request->divisi;
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $tahp) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('tahap', '=', $tahp);
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif (!empty($request->tahap) && empty($request->divisi) && !empty($request->search)){
                $tahp = $request->tahap;
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp, $key) {
                    $q->where('tahap', '=', $tahp);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif (empty($request->tahap) && !empty($request->divisi) && !empty($request->search)){
                $div = $request->divisi;
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $key) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }elseif (!empty($request->tahap) && !empty($request->divisi) && !empty($request->search)){
                $div = $request->divisi;
                $key = $request->search;
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $key, $tahp) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                    $q->where('tahap', '=', $tahp);
                })
                    ->orWhere('user_maker', Auth::user()->personal_number)
                    ->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)
                    ->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)
                    ->get();
            }
        }else{
            $temp = [3,4,5,6];
            if (empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif (!empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('tahap', '=', $tahp);
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif (empty($request->tahap) && !empty($request->divisi) && empty($request->search)){
                $div = $request->divisi;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div) {
                    $q->where('divisi_id', '=', $div);
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif(empty($request->tahap) && empty($request->divisi) && !empty($request->search)){
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ( $key) {
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif (!empty($request->tahap) && !empty($request->divisi) && empty($request->search)){
                $div = $request->divisi;
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $tahp) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('tahap', '=', $tahp);
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif (!empty($request->tahap) && empty($request->divisi) && !empty($request->search)){
                $tahp = $request->tahap;
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp, $key) {
                    $q->where('tahap', '=', $tahp);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif (empty($request->tahap) && !empty($request->divisi) && !empty($request->search)){
                $div = $request->divisi;
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $key) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
            }elseif (!empty($request->tahap) && !empty($request->divisi) && !empty($request->search)){
                $div = $request->divisi;
                $key = $request->search;
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $key, $tahp) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                    $q->where('tahap', '=', $tahp);
                })
                    ->whereIn('flag_mcs', $temp)
                    ->get();
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
