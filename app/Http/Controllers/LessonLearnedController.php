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

            if (empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $query = Project::with(['lesson_learned'])->get();
            }elseif (!empty($request->tahap) && empty($request->divisi) && empty($request->search)){
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('tahap', '=', $tahp);
                })->get();
            }elseif (empty($request->tahap) && !empty($request->divisi) && empty($request->search)){
                $div = $request->divisi;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div) {
                    $q->where('divisi_id', '=', $div);
                })->get();
            }elseif(empty($request->tahap) && empty($request->divisi) && !empty($request->search)){
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ( $key) {
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })->get();
            }elseif (!empty($request->tahap) && !empty($request->divisi) && empty($request->search)){
                $div = $request->divisi;
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $tahp) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('tahap', '=', $tahp);
                })->get();
            }elseif (!empty($request->tahap) && empty($request->divisi) && !empty($request->search)){
                $tahp = $request->tahap;
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($tahp, $key) {
                    $q->where('tahap', '=', $tahp);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })->get();
            }elseif (empty($request->tahap) && !empty($request->divisi) && !empty($request->search)){
                $div = $request->divisi;
                $key = $request->search;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $key) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                })->get();
            }elseif (!empty($request->tahap) && !empty($request->divisi) && !empty($request->search)){
                $div = $request->divisi;
                $key = $request->search;
                $tahp = $request->tahap;
                $query = Project::whereHas('lesson_learned', function ($q) use ($div, $key, $tahp) {
                    $q->where('divisi_id', '=', $div);
                    $q->where('lesson_learned', 'LIKE', '%'.$key.'%');
                    $q->where('tahap', '=', $tahp);
                })->get();
            }

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $query
            ],200);
    }
}
