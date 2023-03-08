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
        $lessonLearn = Project::with(['lesson_learned'])->join('divisis', 'projects.divisi_id', '=', 'divisis.id')->where('flag_mcs', 5)
            ->select(DB::raw('projects.id as id, nama, slug, flag_mcs, projects.created_at, projects.updated_at, divisi_id, divisis.direktorat'))->orderBy('updated_at', 'DESC');

        if (!isset($request->tahap) && !isset($request->direktorat) && !isset($request->divisi) && !isset($request->search)) {
            $query = $lessonLearn;
        }
        if (isset($request->tahap)){
            $tahp = $request->tahap;
            if ($tahp !== 0) {
                $query = $lessonLearn->whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('tahap', '=', $tahp);
                });
            } else {
                $query = $lessonLearn->whereHas('lesson_learned', function ($q) use ($tahp) {
                    $q->where('tahap', '=', 0);
                });
            }
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

        $data = $query->paginate(10);
        $count = count($data);

        $paginate   = view('manage_lesson.paginate',compact('data'))->render();
        $data['paginate'] = $paginate;
        $data['total'] = $count;

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $data
        ],200);
    }
}
