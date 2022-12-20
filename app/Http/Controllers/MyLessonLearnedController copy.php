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
            $query = Project::with(['consultant','divisi','keywords', 'lesson_learned','project_managers', 'document'])->where(function($q){
                $q->orWhere('user_maker', Auth::user()->personal_number);
                $q->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1);
                $q->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2);
            })->orderBy('created_at', 'DESC')->get();
        }elseif (Auth::User()->role == 3) {
            $temp = [3,4,5,6];
            $query = Project::with(['consultant','divisi','keywords', 'lesson_learned','project_managers', 'document'])->whereIn('flag_mcs', $temp)->orderBy('created_at', 'DESC')->get();
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
            if (empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])
                    ->where('flag_mcs', 5)
                    ->get();
            } else if (empty($request->tahap) && empty($request->divisi) && empty($request->search)){
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
