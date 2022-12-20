<?php

namespace App\Http\Controllers;

use App\CommunicationSupport;
use App\Divisi;
use App\Implementation;
use App\Project;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class DivisiController extends Controller
{
    public function getDivisi($id)
    {
        // $query = Divisi::with('project')->find($id);
        $query = Divisi::with(['project' => function($data) {
            $data->orderBy('tanggal_mulai', 'desc');
            $data->where('flag_mcs', 5);
        }, 'project.consultant'])->find($id);

        $queryInit = DB::table('communication_support')
            ->join('projects', 'communication_support.project_id', '=', 'projects.id')
            ->select('communication_support.*')
            ->where('projects.divisi_id', '=', $id)
            ->where('communication_support.status', '=', 'publish')
            ->get();

        $queryStra = Project::whereHas('communication_support', function($q) {
            $q->where('status', 'publish');
        })
        ->where('divisi_id', $id)->get();

        $queryImpl = Implementation::with(['favorite_implementation' => function ($q) {
            $q->where('user_id', Auth::user()->id);
        }])
            ->join('projects', 'implementation.project_id', '=', 'projects.id')
            ->select(DB::raw('implementation.id, implementation.title, implementation.thumbnail, implementation.desc_piloting, 
                implementation.desc_roll_out, implementation.desc_sosialisasi, implementation.views, implementation.slug'))
            ->where('implementation.status', 'publish')
            ->where('projects.divisi_id', '=', $id)
            ->get();

        $thn_mulai = array();
        foreach ($query->project as $s) {
            $pisah = Carbon::parse($s->tanggal_mulai);
            if (isset($thn_mulai[$pisah->year])) {
                $thn_mulai[$pisah->year] += 1 ;
            } else {
                $thn_mulai[$pisah->year] = 1 ;
            }
        }
        // return $query;

        try {
            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $query;
            $data['data_cominit']=  $queryInit;
            $data['data_stra']  = $queryStra;
            $data['data_impl']  = $queryImpl;
            $data['tahun']      =   $thn_mulai;
            Log::info('ASU INI APA DIV', [$data]);
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

    public function getDivisiByDirektorat($sample)
    {
        try {
            $sample = str_replace('-', ' ', $sample);

            if ($sample === 'NULL') {
                $query = Divisi::where('direktorat',NULL)->get();
            }else{
                $query = Divisi::where('direktorat',$sample)->get();
            }

            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $query;
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

    public function getDir($dir){
        try {
            $dir = str_replace('-', ' ', $dir);
            $dir = str_replace('%20', ' ', $dir);


            if ($dir === 'NULL') {
                $query = Divisi::where('direktorat', NULL)->get();
            } else {
                $query = Divisi::where('direktorat', $dir)->get();
            }

            $tempInit = [];
            $tempStra = [];
            $tempImpl = [];
            foreach ($query as $itemDiv) {
                $queryInit = DB::table('communication_support')
                    ->join('projects', 'communication_support.project_id', '=', 'projects.id')
                    ->select('communication_support.*')
                    ->where('projects.divisi_id', '=', $itemDiv->id)
                    ->where('communication_support.status', '=', 'publish')
                    ->get();

                $queryStra = Project::whereHas('communication_support', function ($q) {
                    $q->where('status', 'publish');
                })
                    ->where('divisi_id', $itemDiv->id)->get();

                $queryImpl = Implementation::with(['favorite_implementation' => function ($q) {
                    $q->where('user_id', Auth::user()->id);
                }])
                    ->join('projects', 'implementation.project_id', '=', 'projects.id')
                    ->select(DB::raw('implementation.id, implementation.title, implementation.thumbnail, implementation.desc_piloting, 
                implementation.desc_roll_out, implementation.desc_sosialisasi, implementation.views, implementation.slug'))
                    ->where('implementation.status', 'publish')
                    ->where('projects.divisi_id', '=', $itemDiv->id)
                    ->get();


                if (!empty($queryInit)) {
                    $tempInit[] = $queryInit;
                    $tempStra[] = $queryStra;
                    $tempImpl[] = $queryImpl;
                }
            }


            $data['message'] = 'GET Berhasil.';
            $data['data_divisi'] = $query;
            $data['data_init'] = $tempInit;
            $data['data_stra'] = $tempStra;
            $data['data_impl'] = $tempImpl;
            Log::info('ASU INI APA DARAN', [$data]);
            return response()->json([
                "status" => 1,
                "data" => $data,
            ], 200);
        } catch (\Throwable $th) {
            $data['message'] = 'GET Gagal';
            return response()->json([
                'status' => 0,
                'data' => $data
            ], 200);
        }
    }

    public function getALlDivisi(){
        try{
            $query              = Divisi::select('direktorat')->groupBy('direktorat')->get();
            $queryDiv           = Divisi::get();
            $data['message']    =   'GET Berhasil.';
            $data['direktorat'] =   $query;
            $data['divisi']     = $queryDiv;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        }catch (\Throwable $th){
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }
}
