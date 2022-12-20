<?php

namespace App\Http\Controllers;

use App\Consultant;
use App\Divisi;
use App\Events\TriggerActivityEvent;
use App\Keyword_log;
use App\Keywords;
use App\Lesson_learned;
use App\Project;
use App\Search_log;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use stdClass;
use File;
use Illuminate\Support\Facades\Storage;
use Validator;
use ZipArchive;

class ProjectController extends Controller
{
    public function index($slug)
    {
        $sample = Project::with(['consultant', 'document', 'favorite_project'=> function($q){
            $q->where('user_id',Auth::User()->id);
        },'comment' => function($q){
            $q->where('parent_id',null);
            $q->orderby('created_at','desc');
            $q->with(['child' => function($q2){
                $q2->orderby('created_at','desc');
            }]);
        }])->where('slug',$slug)->first();
        $q = Project::with(['user_restrict'])->where('slug',$slug)->first();
        $is_allowed = 0; //dilarang
        if ($q->is_restricted == 1) { //ketika PROJECT INI SIFATNYA RESTRICTED maka di cek siapa aja yg boleh liat
            if (!empty($q->user_restrict)){ //cek table restriction yg di allow disana siapa aja
                foreach ($q->user_restrict as $restricted) {
                    if($restricted->user_id == Auth::user()->personal_number) { //ketika yg di allow match dengan yg lagi login maka..
                        $is_allowed = 1; //diizinkan
                        break;
                    }
                }
            }
        } else { //jika tidak bersifat RESTRICTED maka siapa saja yg akses dengan user apapun bisa lolos liat
            $is_allowed = 1;
        }

        $is_favorit = false;
        if ($sample->favorite_project) {
            foreach ($sample->favorite_project as $fav) {
                if ($fav->user_id == Auth::user()->id) {
                    $is_favorit = true;
                    break;
                }
            }
        }

        // if (file_exists(public_path('storage/'.$sample->thumbnail))) {
        //     $sample->thumbnail = config('app.BE_url').'storage/'.$sample->thumbnail;
        // }else{
        //     $sample->thumbnail = config('app.FE_url').'assets/img/boxdefault.svg';
        // }

        // ----------------------------------
        // log pencarian
        $log['project_id'] = $sample->id;
        $log['user_id'] = Auth::user()->id;
        Search_log::create($log);
        // ----------------------------------

        // Event Activity
        event(new TriggerActivityEvent(8, Auth::user()->personal_number));

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $sample;
            $data['is_favorit']     =   $is_favorit;
            $data['is_allowed']     =   $is_allowed??0;
            $data['tgl_mulai']      =   $sample->tanggal_mulai;
            $data['tgl_selesai']    =   $sample->tanggal_selesai;
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function all(Request $request){
        $tahp = $request->tahap;
        $div = $request->divisi;
        $con = $request->consultant;
        $key = $request->search;
        $sort = $request->sort;

        $arr_tahap = explode('-', $tahp);
        $tahap = function ($q) use ($arr_tahap) {
            $q->whereIn('tahap', $arr_tahap);
        };

        $arr_divisi = explode('-', $div);
        $divisi = function ($q) use ($arr_divisi) {
            $q->whereIn('divisi_id', $arr_divisi);
        };

        $arr_consul = explode('-', $con);
        $consul = function ($q) use ($arr_consul) {
            $q->whereIn('consultant_id', $arr_consul);
        };

        $search = function ($q) use ($key) {
            $q->where('nama', 'LIKE', '%'.$key.'%');
        };

        // kondisi tahap
        if (empty($tahp) && empty($div) && empty($con) && empty($key) && empty($sort)){
            $query = Project::with(['lesson_learned'])->get();
        } elseif (empty($tahp) && empty($div) && empty($con) && empty($key) && !empty($sort)){
            $query = Project::with(['lesson_learned'])->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && empty($div) && empty($con) && empty($key) && empty($sort)){
            $query = Project::whereHas('lesson_learned', $tahap)->get();
        } elseif (!empty($tahp) && empty($div) && empty($con) && empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && !empty($div) && empty($con) && empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $divisi)->get();
        } elseif (!empty($tahp) && !empty($div) && empty($con) && empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $divisi)->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && !empty($div) && !empty($con) && empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->get();
        } elseif (!empty($tahp) && !empty($div) && !empty($con) && empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && !empty($div) && !empty($con) && !empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->whereHas('lesson_learned', $search)->get();
        } elseif (!empty($tahp) && !empty($div) && !empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->whereHas('lesson_learned', $search)->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && empty($div) && !empty($con) && empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('consultant', $consul)->get();
        } elseif (!empty($tahp) && empty($div) && !empty($con) && empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('consultant', $consul)->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && empty($div) && !empty($con) && !empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('consultant', $consul)->whereHas('lesson_learned', $search)->get();
        } elseif (!empty($tahp) && empty($div) && !empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('consultant', $consul)->whereHas('lesson_learned', $search)->orderBy('nama', $sort)->get();
        } elseif (!empty($tahp) && empty($div) && empty($con) && !empty($key) && empty($sort)){
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $search)->get();
        } elseif (!empty($tahp) && empty($div) && empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $tahap)->whereHas('lesson_learned', $search)->orderBy('nama', $sort)->get();
        } 

        // kondisi divisi
        elseif (empty($tahp) && !empty($div) && empty($con) && empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->get();
        } elseif (empty($tahp) && !empty($div) && empty($con) && empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->orderBy('nama', $sort)->get();
        } elseif (empty($tahp) && !empty($div) && !empty($con) && empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->get();
        } elseif (empty($tahp) && !empty($div) && !empty($con) && empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->orderBy('nama', $sort)->get();
        } elseif (empty($tahp) && !empty($div) && empty($con) && !empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->whereHas('lesson_learned', $search)->get();
        } elseif (empty($tahp) && !empty($div) && empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->whereHas('lesson_learned', $search)->orderBy('nama', $sort)->get();
        } elseif (empty($tahp) && !empty($div) && !empty($con) && !empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->whereHas('lesson_learned', $search)->get();
        } elseif (empty($tahp) && !empty($div) && !empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $divisi)->whereHas('consultant', $consul)->whereHas('lesson_learned', $search)->orderBy('nama', $sort)->get();
        }

        // kondisi consultant
        elseif (empty($tahp) && empty($div) && !empty($con) && empty($key) && empty($sort)) {
            $query = Project::with(['lesson_learned'])->whereHas('consultant', $consul)->get();
        } elseif (empty($tahp) && empty($div) && !empty($con) && empty($key) && !empty($sort)) {
            $query = Project::with(['lesson_learned'])->whereHas('consultant', $consul)->orderBy('nama', $sort)->get();
        } elseif (empty($tahp) && empty($div) && !empty($con) && !empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $search)->whereHas('consultant', $consul)->get();
        } elseif (empty($tahp) && empty($div) && !empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $search)->orderBy('nama', $sort)->whereHas('consultant', $consul)->orderBy('nama', $sort)->get();
        }

        // kondisi search
        elseif (empty($tahp) && empty($div) && empty($con) && !empty($key) && empty($sort)) {
            $query = Project::whereHas('lesson_learned', $search)->get();
        } elseif (empty($tahp) && empty($div) && empty($con) && !empty($key) && !empty($sort)) {
            $query = Project::whereHas('lesson_learned', $search)->get();
        } 

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $query
        ],200);
    }

    public function projectById($id) {
        try {
            $data = Project::where('id', $id)->first();
            if (!$data) {
                $data_error['message'] = 'Project Not Found';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            return response()->json([
                'status' => 1,
                'data'  => $data
            ], 400);
        } catch (\Throwable $th) {
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data,
                'error'     =>  $th
            ],200);
        }
    }

    public function bahan_filter(){
        try {
            $direktorat = Divisi::select('direktorat')->distinct('direktorat')->get();
            $consultant = Consultant::get();
            $tahun      = Project::orderby('tanggal_mulai','desc')->distinct()->get(['tanggal_mulai']);
            $lessonlearn = Lesson_learned::distinct()->get(['tahap']);
            $keywords   = Keywords::select('nama', DB::raw('count(*) as num'))->groupby('nama')->orderby('num','desc')->get();

            $tampung = [];
            $temp=[];
            foreach ($direktorat as $key) {
                $div     = Divisi::where('direktorat',$key->direktorat)->get();
                $temp2=[];
                foreach ($div as $key2) {
                    $object             = new stdClass;
                    $object->divisi     =   $key2->divisi;
                    $object->id  =   $key2->id;
                    $object->shortname  =   $key2->shortname;
                    $temp2[]            =   $object;
                }
                $object             = new stdClass;
                $object->direktorat =   $key->direktorat;
                $object->divisi     =   $temp2;
                $temp[]            =   $object;
            }
            $divisi = $temp;
            $tampung = [];
            $temp_cek = [];
            foreach ($tahun as $key) {
                $tempY          = Carbon::create($key->tanggal_mulai->toDateTimeString())->format('Y');
                $tempM          = Carbon::create($key->tanggal_mulai->toDateTimeString())->format('m');
                $tempM_name     = Carbon::create($key->tanggal_mulai->toDateTimeString())->format('F');

                $object = new stdClass;
                $object->bulan          =   $tempM;
                $object->bulan_name     =   $tempM_name;
                if (! isset($temp_cek[$tempY][$tempM])) {
                    // cek handler
                    $temp_cek[$tempY][$tempM]   =   $tempM;

                    //tampung
                    $year[$tempY][]  =   $object;
                }
            }

            $data['tahun']      =   $year;
            $data['tahun_asli'] =   $tahun;
            $data['divisi']     =   $divisi;
            $data['consultant'] =   $consultant;
            $data['lessonlearn'] =   $lessonlearn;
            $data['keywords']   =   $keywords;
            // $data['project'] = $query;

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

    public function filterisasi(){
        try {
            $sort       =   request()->sort??"disabled";
            $sort2      =   request()->sort2??"disabled";
            $from       =   request()->from??0;
            $search     =   request()->search??'*';

            // keyword paling trending handlers
            $cek_keyword = str_replace('*', '', $search);
            $cek = Keywords::where('nama',$cek_keyword)->first();
            if (isset($cek->nama)) {
                $tambah_log['user_id']  =   Auth::User()->id;
                $tambah_log['nama']     =   $cek->nama;
                Keyword_log::create($tambah_log);
            }

            $potongan = explode(" ", $search);

            $divisi     =   explode(',', request()->divisi)??[];
            $divisi     =   $divisi ==  [""]    ?  $divisi=[] : $divisi;

            $tampungconsultant  = request()->consultant;
            $consultant         =   explode(',', $tampungconsultant)??[];
            $consultant         =   $consultant ==  [""]    ?  $consultant=[] : $consultant;

            $tampunglessonlearn  = request()->lessonlearn;
            $lessonlearn         =   explode(',', $tampunglessonlearn)??[];
            $lessonlearn         =   $lessonlearn ==  [""]    ?  $lessonlearn=[] : $lessonlearn;

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
                    "size"         => 10,
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
                    if ($potongan) {
                        foreach ($potongan as $p) {
                            $search = implode(', ', $potongan);
                            $match              =   new stdClass;
                            $match->wildcard    =   ["nama"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                            $match              =   new stdClass;
                            $match->wildcard    =   ["deskripsi"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                            $match              =   new stdClass;
                            $match->wildcard    =   ["consultant.nama"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                            $match              =   new stdClass;
                            $match->wildcard    =   ["lesson_learneds.tahap"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                            $match              =   new stdClass;
                            $match->wildcard    =   ["project_managers"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                            $match              =   new stdClass;
                            $match->wildcard    =   ["divisi"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                            $match              =   new stdClass;
                            $match->wildcard    =   ["keywords.nama"   => str_replace('"',"",$p)];
                            $searchs[]          =  $match;
                        }
                    } else {
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
                        $match->wildcard    =   ["lesson_learneds.tahap"   => str_replace('"',"",$search)];
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
                    }


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

            // return response()->json([
            //     "status"    => '1',
            //     "data"      => $postData
            // ],200);

            curl_setopt($ch, CURLOPT_URL,config('app.ES_url')."/project/_search");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData,JSON_PRETTY_PRINT));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result     = curl_exec ($ch);
            $hasil = json_decode($result);
            // cek
            // return response()->json($hasil);
            if (isset($hasil->hits->hits)) {
                    $data['data']           = $hasil->hits->hits;
                    // $data['datakonsultant'] = $tampungkonsultant;
                    $data['count']          = $hasil->hits->total->value;
            }else{
                $data['data']         = [];
                $data['count']        = 0;
            }
            $data['message']    =   "Berhasil";
            $data['searchs']    =   $searchs;
            return response()->json([
                "status"    => '1',
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   "Terjadi Kesalahan, Coba Sesaat lagi";
            return response()->json([
                "status"    => '0',
                "data"      => $data
            ]);
        }
    }

    public function preview($slug)
    {
        $sample = Project::with(['consultant','divisi','keywords','lesson_learned','project_managers','document','comment' => function($q){
            $q->where('parent_id',null);
            $q->orderby('created_at','desc');
            $q->with(['child' => function($q2){
                $q2->orderby('created_at','desc');
            }]);
        }])->where('slug',$slug)->first();
        $q = Project::with(['user_restrict'])->where('slug',$slug)->first();
        $is_allowed = 0; //dilarang
        if ($q->is_restricted == 1) { //ketika PROJECT INI SIFATNYA RESTRICTED maka di cek siapa aja yg boleh liat
            if (!empty($q->user_restrict)){ //cek table restriction yg di allow disana siapa aja
                foreach ($q->user_restrict as $restricted) {
                    if($restricted->user_id == Auth::user()->personal_number) { //ketika yg di allow match dengan yg lagi login maka..
                        $is_allowed = 1; //diizinkan
                        break;
                    }
                }
            }
        } else { //jika tidak bersifat RESTRICTED maka siapa saja yg akses dengan user apapun bisa lolos liat
            $is_allowed = 1;
        }

        $is_favorit = false;
        if ($sample->favorite_project) {
            foreach ($sample->favorite_project as $fav) {
                if ($fav->user_id == Auth::user()->id) {
                    $is_favorit = true;
                    break;
                }
            }
        }

        // ----------------------------------
        // log pencarian
        $log['project_id'] = $sample->id;
        $log['user_id'] = Auth::user()->id;
        Search_log::create($log);
        // ----------------------------------

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $sample;
            $data['is_favorit']     =   $is_favorit;
            $data['is_allowed']     =   $is_allowed??0;
            $data['tgl_mulai']      =   $sample->tanggal_mulai;
            $data['tgl_selesai']    =   $sample->tanggal_selesai;
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function archieve(Request $request){
        try {
            $tampung = $request->bahan;
            // Zip File Name
            $zipFileName = 'download.zip';

            // Create ZipArchive Obj
            // chdir( sys_get_temp_dir() );
            // $zip = new ZipArchive;

            // // path user
            // $path = public_path('temp_download/'.Auth::user()->id);
            // $path_zip = public_path('temp_download/'.Auth::user()->id.'/ex');
            // File::deleteDirectory($path);
            // File::makeDirectory($path_zip, 775, true, true);

            // if ($zip->open($path_zip."/".$zipFileName, ZipArchive::CREATE) === TRUE) {
            //     for ($i=0; $i < count($tampung); $i++) {
            //         $file = storage_path("app/public/".$tampung[$i]);
            //         $relativename = basename($file);
            //         $zip->addFile($file, $relativename);
            //     }

            //     $zip->close();
            //     return response()->json([
            //         'status'    =>  1,
            //         'data'      =>  config('app.BE_url').'temp_download/'.Auth::user()->id.'/ex'."/".$zipFileName
            //     ],200);
            // }else{
            //     $datas['message']    =   'Something Problem';
            //     return response()->json([
            //         'status'    =>  0,
            //         'data'      =>  $datas
            //     ],200);
            // }

            // destination
            $path_zip = public_path('temp_download/'.Auth::user()->id.'/ex');
            File::deleteDirectory($path_zip);
            File::makeDirectory($path_zip, 777, true, true);

            // temp
            chdir( sys_get_temp_dir() ); // Zip always get's created in current working dir so move to tmp.
            $zip = new ZipArchive;
            $tmp_zipname = 'download_lampiran.zip'; // Generate a temp UID for the file on disk.
            // $zipname = 'package_name.zip'; // True filename used when serving to user.
            if ( true === $zip->open( $tmp_zipname, ZipArchive::CREATE ) ) {
                // $zip->addFromString( 'file_name.txt', 'asdasdasddsasd' );
                for ($i=0; $i < count($tampung); $i++) {
                    $file = storage_path("app/public/".$tampung[$i]);
                    $relativename = basename($file);
                    $zip->addFile($file, $relativename);
                }
                $zip->close();

                // permission
                chmod(sys_get_temp_dir(). '/' .$tmp_zipname, 0777);
                chmod(public_path('temp_download/'.Auth::user()->id.'/ex'), 0777);

                // move to folder public
                copy(
                    sys_get_temp_dir(). '/' .$tmp_zipname,
                    public_path('temp_download/'.Auth::user()->id.'/ex/'.$zipFileName));

                // tutup
                chmod(public_path('temp_download/'.Auth::user()->id.'/ex'), 0755);
                unlink( sys_get_temp_dir(). '/' .$tmp_zipname );

                return response()->json([
                    'status'    =>  1,
                    'data'      =>  config('app.BE_url').'temp_download/'.Auth::user()->id.'/ex'."/".$zipFileName
                ],200);
            }else{
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  false
                ],200);
            }

        } catch (\Throwable $th) {
            $datas['message']    =   'Something Problem';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }
}
