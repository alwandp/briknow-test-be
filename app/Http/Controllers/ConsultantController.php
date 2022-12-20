<?php

namespace App\Http\Controllers;

use App\Consultant;
use App\ConsultantLog;
use App\Project;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function index($id)
    {
        $sample = Consultant::with(['project' => function($query) {
            $query->with('consultant')->orderBy('tanggal_mulai', 'desc')->where('flag_mcs',5);
        }])->with('favorite_consultant')->find($id);

        $thn_mulai = array();
        foreach ($sample->project as $s) {
            $pisah = Carbon::parse($s->tanggal_mulai);
            if (isset($thn_mulai[$pisah->year])) {
                $thn_mulai[$pisah->year] += 1 ;
            } else {
                $thn_mulai[$pisah->year] = 1 ;
            }
        }

        $is_favorit = false;
        if ($sample->favorite_consultant) {
            foreach ($sample->favorite_consultant as $fav) {
                if ($fav->user_id == Auth::user()->id) {
                    $is_favorit = true;
                    break;
                }
            }
        }

        // ----------------------------------
        // log pencarian
        $log['consultant_id'] = $sample->id;
        $log['user_id'] = Auth::user()->id;
        ConsultantLog::create($log);
        // ----------------------------------

        try {
            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $sample;
            $data['is_favorit']       =   $is_favorit;
            $data['tahun']      =   $thn_mulai;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function getConsultant()
    {
        $sample = Consultant::get();

        try {
            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $sample;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }
}
