<?php

namespace App\Http\Controllers;

use App\Consultant;
use App\Divisi;
use App\Document;
use App\Keyword_log;
use App\Keywords;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class PencarianController extends Controller
{
    public function cariall(){
        try {
            // elastic Search
            $data = project::search('')->get();

            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Pencarian Data Failed';
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $data
            ],400);
        }
    }

    public function cari($katakunci=""){
        try {
            // old
            // $data = DB::table('projects')
            //     ->join('consultants','consultants.id','=','projects.consultant_id')
            //     ->join('divisis','divisis.id','=','projects.divisi_id')
            //     ->join('project_managers','project_managers.id','=','projects.project_managers_id')
            //     ->orWhere('projects.nama','like', "%$katakunci%")
            //     ->orWhere('consultants.nama','like', "%$katakunci%")
            //     ->orWhere('divisis.divisi','like', "%$katakunci%")
            //     ->orWhere('project_managers.nama','like', "%$katakunci%")
            //     ->select('projects.*','consultants.*','divisis.*','project_managers.*','projects.nama as nama_project','consultants.nama as nama_consultant','project_managers.nama as nama_project_managers','projects.id as id')
            //     ->get();

            // elastic Search
            // $data = project::search($katakunci)->get();

            // keyword paling trending handlers
            $cek_keyword = $katakunci;
            $cek = Keywords::where('nama',$cek_keyword)->first();
            if (isset($cek->nama)) {
                $tambah_log['user_id']  =   Auth::User()->id;
                $tambah_log['nama']     =   $cek->nama;
                Keyword_log::create($tambah_log);
            }

            $sort       =   "disabled";
            $sort2      =   "disabled";
            $from       =   0;
            $search     =   '*'.$katakunci.'*'??'*';

            $divisi     =   explode(',', request()->divisi)??[];
            $divisi     =   $divisi ==  [""]    ?  $divisi=[] : $divisi;

            $consultant =   explode(',', request()->konsultant)??[];
            $consultant =   $consultant ==  [""]    ?  $consultant=[] : $consultant;

            $tahun      =   explode(',', request()->tahun)??[];
            $tahun      =   $tahun ==  [""]    ?  $tahun=[] : $tahun;

            $ch = curl_init();
            $headers  = [
                        'Content-Type: application/json',
                        'Accept: application/json',
                    ];
                    
            // postdata
                $postData = [
                    "from"         => $from,
                    "size"         => 10000,
                ];

            // sort control
                $objectsort         =   new stdClass();
                if ($sort == 'disabled' && $sort2 == 'disabled') {
                }elseif ($sort <> 'disabled' && $sort2 == 'disabled') {
                    $objectsort->tanggal_mulai  =   $sort;
                    $filtertambah['sort']       =   $objectsort;
                }elseif ($sort == 'disabled' && $sort2 <> 'disabled') {
                    $objectsort->nama           =   $sort2;
                    $filtertambah['sort']       = $objectsort;
                }else{
                    $objectsort->tanggal_mulai  =   $sort;
                    $objectsort->nama           =   $sort2;
                    $filtertambah['sort']       = $objectsort;
                }
                if ($sort <> 'disabled' || $sort2 <> 'disabled') {
                    $postData+= $filtertambah;
                }

            // query control
                $must   =   [];
                // search
                    $searchs        =   [];
                    
                    $match              =   new stdClass;
                    $match->wildcard    =   ["nama"   => str_replace('"',"",$search)];  
                    $searchs[]          =  $match;
                    $match              =   new stdClass;
                    $match->wildcard    =   ["deskripsi"   => str_replace('"',"",$search)];  
                    $searchs[]          =  $match;
                    $match              =   new stdClass;
                    $match->wildcard    =   ["consultant.nama"   => str_replace('"',"",$search)];  
                    $searchs[]          =  $match;
                    $match              =   new stdClass;
                    $match->wildcard    =   ["project_managers"   => str_replace('"',"",$search)];  
                    $searchs[]          =  $match;
                    $match              =   new stdClass;
                    $match->wildcard    =   ["divisi"   => str_replace('"',"",$search)];  
                    $searchs[]          =  $match;
                    $match              =   new stdClass;
                    $match->wildcard    =   ["keywords.nama"   => str_replace('"',"",$search)];  
                    $searchs[]          =  $match;

                    // ------------------
                    $should             =   new stdClass;
                    $should->bool       =   ["should"   => $searchs];  
                    $must[]             =  $should;

                // filter
                    // flag mcs
                    // $mcs            =   [];
                        $match          =   new stdClass;
                        $match->match   =   ["flag_mcs"   =>  5];  
                        $must[]   =  $match;
                    
                    // consultant
                        $consultants        =   [];
                        if (!empty($consultant)) {
                            if (count($consultant) > 0) {
                                for ($i=0; $i < count($consultant); $i++) { 
                                    // $consult        =   [];
                                    $match          =   new stdClass;
                                    $match->match   =   ["consultant.nama"   => str_replace('"',"",$consultant[$i])];  
                                    $consultants[]         =  $match;
                                }
                            }
                        }
                        $should             =   new stdClass;
                        $should->bool       =   ["should"   => $consultants];  
                        $must[]             =  $should;

                    // divisi
                        $divisis        =   [];
                        if (!empty($divisi)) {
                            if (count($divisi) > 0) {
                                for ($i=0; $i < count($divisi); $i++) { 
                                    // $consult        =   [];
                                    $match          =   new stdClass;
                                    $match->match   =   ["divisi"   => str_replace('"',"",$divisi[$i])];  
                                    $divisis[]         =  $match;
                                }
                            }
                        }
                        $should             =   new stdClass;
                        $should->bool       =   ["should"   => $divisis];  
                        $must[]             =  $should;

                    // tahun
                        $year        =   [];
                        if (!empty($tahun)) {
                            if (count($tahun) > 0) {
                                for ($i=0; $i < count($tahun); $i++) { 
                                    $t                          =   str_replace('"',"",$tahun[$i]);
                                    $range                      =   new stdClass;
                                    $contentrange               =   new stdClass;
                                    $contentrange->time_zone    =   "+01:00";
                                    $contentrange->gte          =   "$t-01T00:00:00";
                                    $contentrange->lte          =   "$t-31T00:00:00";
                                    $range->range               =   ["tanggal_mulai"   => $contentrange];  
                                    $year[]                     =  $range;
                                }
                            }
                        }
                        $should             =   new stdClass;
                        $should->bool       =   ["should"   => $year];  
                        $must[]             =  $should;

                // supply
                    $m['must']                  =   $must;
                    $bool['bool']               =   $m;
                    $search_filter['query']     =   $bool;
                    $postData                   +=  $search_filter;

            curl_setopt($ch, CURLOPT_URL,config('app.ES_url')."/project/_search");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData,JSON_PRETTY_PRINT));           
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result     = curl_exec ($ch);
            $hasil = json_decode($result);
            // return response()->json($hasil);
            // cek
            // return response()->json($hasil);
            if (isset($hasil->hits->hits)) {
                    $data['data']         = $hasil->hits->hits;
                    $data['count']        = $hasil->hits->total->value;
            }else{
                $data['data']         = [];
                $data['count']        = 0;
            }
            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Pencarian Data Failed';
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $data
            ],400);
        }
    }

    public function detail($id){
        try {
            // $data = DB::table('projects')
            //     ->join('divisis','divisis.id','=','projects.divisi_id')
            //     ->join('project_managers','project_managers.id','=','projects.project_managers_id')
            //     ->join('consultant_projects','consultant_projects.project_id','=','projects.id')
            //     ->join('consultants','consultants.id','=','consultant_projects.consultant_id')
            //     ->orWhere('projects.id',"$id")
            //     ->select('projects.*','consultants.*','divisis.*','project_managers.*','projects.nama as nama_project','project_managers.nama as nama_project_managers','projects.id as id')
            //     ->get();


            $data = Project::with('consultant')->where('id',$id)->first();
            $keyword = Keywords::where('project_id',$id)->get();

            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data,
                'keywords'  =>  $keyword
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Failed';
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $data
            ],400);
        }
    }

    public function doc_project($kunci = "",$search="*",$sort="desc"){
        try {
            // rubah search
            $search = str_replace('_', ' ', $search);

            if ($search == "*") {
                $data = Document::where('project_id',$kunci)->orderby('updated_at',$sort)->get();
            }else{
                $data = Document::where('project_id',$kunci)->where('nama','like','%'.$search.'%')->orwhere('jenis_file','like','%'.$search.'%')->orderby('updated_at',$sort)->get();
            }

            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Failed';
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $data
            ],400);
        }
    }

    public function proj_consultant($kunci = "",$search="*",$sort="desc"){
        try {
            // rubah search
            $search = str_replace('_', ' ', $search);

            if ($search == "*") {
                $data = Consultant::with(['project' => function($query) use ($sort) {
                    $query->with('consultant');
                    $query->orderBy('tanggal_mulai', $sort);
                    $query->where('flag_mcs', 5);
                }])->find($kunci);
            }else{
                $data = Consultant::with(['project' => function($query) use ($search,$sort) {
                    $query->with('consultant');
                    $query->where('flag_mcs', 5);
                    $query->where('nama','like','%'.$search.'%');
                    $query->orwhere('deskripsi','like','%'.$search.'%');
                    $query->orwhere('metodologi','like','%'.$search.'%');
                    $query->orderBy('tanggal_mulai', $sort);
                }])->find($kunci);
            }
            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Failed';
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $data
            ],400);
        }
    }

    public function proj_divisi($kunci = "",$search="*"){
        try {
            // rubah search
            $search = str_replace('_', ' ', $search);

            if ($search == "*") {
                $data = Divisi::with(['project' => function($data) {
                    $data->orderBy('tanggal_mulai', 'desc');
                    $data->where('flag_mcs', 5);
                }, 'project.consultant'])->find($kunci);
            }else{
                $data = Divisi::with(['project' => function($data) use ($search) {
                    $data->where(
                        function($q){
                            $q->orderBy('tanggal_mulai', 'desc');
                            $q->where('flag_mcs', 5);
                        }
                    )->Where(
                        function($q)use($search){
                            $q->where('nama','like','%'.$search.'%');
                            $q->orwhere('deskripsi','like','%'.$search.'%');
                            $q->orwhere('metodologi','like','%'.$search.'%');
                        }
                    );
                }, 'project.consultant'])->find($kunci);
            }

            return response()->json([
                'status'    =>  '1',
                'data'      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Failed';
            return response()->json([
                'status'    =>  '0',
                'data'      =>  $data
            ],400);
        }
    }
}
