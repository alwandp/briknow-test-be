<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\Activity;
use App\ActivityUser;
use App\Avatar;
use App\CommunicationSupport;
use App\Consultant;
use App\ConsultantLog;
use App\Divisi;
use App\Implementation;
use App\Keywords;
use App\Lesson_learned;
use App\Level;
use App\Project;
use App\Search_log;
use App\User;
use App\UserAchievement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use stdClass;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function __construct()
    {
    }

    public function index()
    {
            $get            = Project::where('is_recomended',1)->where('flag_mcs',5)->limit(6)->orderby('updated_at','DESC')->get();
            $getowner       = Divisi::withCount(['project' => function($q){
                $q->where('flag_mcs',5);
            }])
            ->orderby('project_count','DESC')->limit(10)->get();
            $getconsultant  = Consultant::withCount('project')->orderby('project_count','DESC')->limit(10)->get();
            $suggest        = Keywords::select('nama', DB::raw('count(*) as num'))->groupby('nama')->orderby('num','desc')->limit(5)->get();
            $leaderboard    = User::orderby('xp','desc')->limit(10)->get();

            $getRecomInnitiative   = CommunicationSupport::with(['favorite_com' => function($q) {
                $q->where('user_id', Auth::user()->id);
            }])->where('status','=', 'publish')
                ->limit(6)->orderby('views','DESC')->orderBy('updated_at','ASC')->get();
            $querydirektorat  = Divisi::select('direktorat')->groupBy('direktorat')->get();
            $queryUker            = Divisi::get();
            $consultant = Consultant::get();
            $allLessonLearn = Lesson_learned::get();
            $lessonlearn = Lesson_learned::distinct()->get(['tahap']);
            $levelling_user = [];
            foreach ($leaderboard as $l){
                $ceklevel               = Level::where('xp', '<=',$l->xp)->orderby('xp','desc')->first();
                $temp                   = new StdClass();
                $temp->badge            = $ceklevel->badge;
                $temp->name             = $l->name;
                $temp->xp               = $l->xp;
                $temp->personal_number  = $l->personal_number;
                $levelling_user[]       = $temp;
            }

            $new    =   [];
            foreach ($get as $item) {
                $obj    =   new stdClass;
                // if (file_exists(public_path('storage/'.$item->thumbnail))) {
                //     $obj->thumbnail = config('app.BE_url').'storage/'.$item->thumbnail;
                // }else{
                //     $obj->thumbnail = config('app.FE_url').'assets/img/boxdefault.svg';
                // }
                $obj->thumbnail     = $item->thumbnail;
                $obj->slug          = $item->slug;
                $obj->divisi        = $item->divisi;
                $obj->nama          = $item->nama;
                $obj->updated_at    = $item->updated_at;
                $new[] = $obj;
            }

            $classInni = [];
            foreach ($getRecomInnitiative as $items){
                $objInni    = new stdClass;
                $objInni->id            = $items->id;
                $objInni->thumbnail     = $items->thumbnail;
                $objInni->slug          = $items->slug;
                $objInni->view          = $items->views;
                $objInni->nama          = $items->title;
                $objInni->type_file     = $items->type_file;
                $objInni->created_at    = $items->created_at;
                $objInni->updated_at    = $items->updated_at;
                $objInni->desc          = $items->desc;
                $objInni->fav           = count($items->favorite_com) > 0 ? 1 : 0;
                $classInni[] = $objInni;
            }

            $data['rekomendasi']    =   $new;
            $data['cominitiative'] =   $classInni;
            $data['owner_project']  =   $getowner;
            $data['consultant']     =   $getconsultant;
            $data['allLessonLearn'] = $allLessonLearn;
            $data['lessonlearn']     =   $lessonlearn;
            $data['consultant_filter']     =   $consultant;
            $data['suggest']        =   $suggest;
            $data['leaderboard']    =   $levelling_user;
            $data['direktorat']     =   $querydirektorat;
            $data['divisi']         =   $queryUker;

            Log::info("INI HASIL GET OWNER BE", [$data]);

        return response()->json([
                "status"    => '1',
                "data"      => $data
            ]);

    }

    public function topfive_vendor(){
        try {
            $num= [];
            $label= [];
            $data=[];
            $vend = ConsultantLog::select('consultant_id', DB::raw('count(consultant_id) quantity'))->groupBy('consultant_id')->orderby('quantity','desc')->limit(5)->get();

            foreach ($vend as $key) {
                $object = new stdClass;
                $object->nama = $key->consultant->nama??'-';
                $object->url = isset($key->consultant->id) ? config('app.FE_url').'consultant/'.$key->consultant->id : config('app.FE_url').'consultant/'.'-';
                $object->jumlahpengunjung = $key->quantity??0;
                $data[] = $object;
            }

            $out['data'] = $data;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $out
            ],200);
        } catch (\Throwable $th) {
            $temp['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $temp
            ],200);
        }
    }

    public function topfive_project($stage="default"){
        try {
            // setting 12 bulan kebelakang

            $num= [];
            $label= [];
            $data=[];
            $vend = project::withCount('search_log')->with('divisi','consultant')
            ->where('flag_mcs', 5)
            ->orderBy('search_log_count', 'desc')
            ->limit(5)
            ->get();
            // return response()->json($temp);
            foreach ($vend as $key) {
                $object = new stdClass;
                $object->namaproject = $key->nama??'-';
                $object->jumlahpengunjung = $key->search_log_count;
                $object->url = config('app.FE_url').'project/'.$key->slug;
                $data[] = $object;
            }

            $out['data'] = $data;

            return response()->json([
                "status"    =>  1,
                "data"      =>  $out
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function suggestionberanda($key)
    {
        try {
            // setting 12 bulan kebelakang
            // elastic Search
            $katakunci  =   $key;

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
            if (isset($hasil->hits->hits)) {
                $data['data']         = $hasil->hits->hits;
            }else{
                $data['data']         = [];
            }
            return response()->json([
                "status"    => '1',
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function profile()
    {
        $query          = User::where('personal_number', Auth::user()->personal_number)->latest()->first();

        $qProjects      = Project::where('user_maker', Auth::user()->personal_number)->orderBy('created_at', 'DESC')->get();
        $qActivities    = Project::where('user_maker', Auth::user()->personal_number)->orderBy('created_at', 'DESC')->get();
        $qLevel         = Level::orderBy('xp', 'ASC')->get();
        $his_activity   = ActivityUser::where('personal_number', Auth::user()->personal_number)->orderBy('created_at', 'ASC')->get();
        $qAvatar        = Avatar::get();


        // cek level user
        $ceklevel   =   Level::where('xp', '<=',Auth::user()->xp)->orderby('xp','desc')->first();
        $level_id   =   $ceklevel->id;

        // count avatar yang sudha di miliki
        $cekleveling            = Level::where('xp', '<=',$query->xp)->orderby('xp','desc')->first();
        $count_avatar           = Avatar::where('level_id','<=',$cekleveling->id)->count();

        // $dataActs['data'] = $qActivities;
        $dataActs['uploaded'] = count($qActivities);
        $dataActs['published'] = count($qActivities->where('flag_mcs', 5));


        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $query;
            $data['projects']       =   $qProjects;
            $data['activities']     =   $dataActs;
            $data['history_activities']   =   $his_activity;
            $data['levels']         =   $qLevel;
            $data['level']          =   $level_id;
            $data['avatar']         =   $qAvatar;
            $data['count_avatar']   =   $count_avatar;
            $data['level_user']     =   $cekleveling;
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

    public function profileuser($pn)
    {
        $query          = User::where('personal_number', $pn)->latest()->first();

        // pengecheckan apakah user terdaftar atau tidak
        if (!isset($query->id)) {
            $datas['message']    =   'User Belum Terdaftar';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }

        $qProjects      = Project::where('user_maker', $pn)->orderBy('created_at', 'DESC')->get();
        $qActivities    = Project::where('user_maker', $pn)->orderBy('created_at', 'DESC')->get();
        $his_activity   = ActivityUser::where('personal_number', $pn)->orderBy('created_at', 'ASC')->get();

        $qLevel         = Level::orderBy('xp', 'ASC')->get();
        $qAvatar        = Avatar::get();

        // cek level user
        $ceklevel   =   Level::where('xp', '<=',$pn)->orderby('xp','desc')->first();
        $level_id   =   $ceklevel->id;

        // count avatar yang sudha di miliki
        $cekleveling            = Level::where('xp', '<=',$query->xp)->orderby('xp','desc')->first();
        $count_avatar           = Avatar::where('level_id','<=',$cekleveling->id)->count();

        // $dataActs['data'] = $qActivities;
        $dataActs['uploaded'] = count($qActivities);
        $dataActs['published'] = count($qActivities->where('flag_mcs', 5));


        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $query;
            $data['projects']       =   $qProjects;
            $data['activities']     =   $dataActs;
            $data['history_activities']   =   $his_activity;
            $data['levels']         =   $qLevel;
            $data['level']          =   $level_id;
            $data['avatar']         =   $qAvatar;
            $data['count_avatar']   =   $count_avatar;
            $data['level_user']     =   $cekleveling;

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

    public function gamification()
    {
        $qUser  = User::where('personal_number', Auth::user()->personal_number)->latest()->first();
        $qAc    = Achievement::get();
        $qLv    = Level::orderby('xp','asc')->get();
        $qAct   = Activity::orderby('xp','asc')->get();

        $tmp['user']            = $qUser;
        $tmp['achievements']    = $qAc;
        $tmp['levels']          = $qLv;
        $tmp['activity']          = $qAct;

        //belum dibuat BE nya

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $tmp;
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

    public function changeavatar(){
        try {
            // tampung
            $temp['avatar_id']  =   request()->avatar;

            // checking
            $data                   = User::where('personal_number',Auth::user()->personal_number)->first();
            $cekleveling            = Level::where('xp', '<=',$data->xp)->orderby('xp','desc')->first();
            $cek_kelayakkan_avatar  = Avatar::where('level_id','<=',$cekleveling->id)->where('id',$temp['avatar_id'])->first();

            if (!isset($cek_kelayakkan_avatar->path)) {
                $dataresponse['message']    =   'Anda Belum Memiliki Avatar Tsb';
                $dataresponse['data']       =   0;
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $dataresponse
                ],200);
            }

            // execute
            $data->update($temp);

            // response
            $dataresponse['message']        =   'Update Avatar Berhasil.';
            $dataresponse['data']           =   $temp['avatar_id'];
            return response()->json([
                "status"    => 1,
                "data"      => $dataresponse,
            ],200);
        } catch (\Throwable $th) {
            $dataresponse['message']    =   'Update Avatar Gagal';
            $dataresponse['data']       =   0;
            return response()->json([
                'status'    =>  0,
                'data'      =>  $dataresponse
            ],200);
        }
    }

    public function congrats(){
        $query          = UserAchievement::where('personal_number',Auth::user()->personal_number)
        ->where('congrats_view',0)
        ->orderby('created_at','asc')
        ->first();

        try {
            // siapkan response
            $data['message']        =   'Get Data Berhasil.';
            $data['data']           =   $query;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Gagal';
            $data['data']       =   0;
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function congrats_update(){
        $id             =   request()->id;
        $query          =   UserAchievement::where('personal_number',Auth::user()->personal_number)->find($id);

        try {
            // update status
            if (isset($query->achievements_id)) {
                $perbaruistatus['congrats_view']    = 1;
                $query->update($perbaruistatus);
            }else{
                $data['message']    =   'update Data ditolak';
                $data['data']       =   0;
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $data
                ],200);
            }
            // siapkan response
            $data['message']        =   'update Data Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'update Data Gagal';
            $data['data']       =   0;
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function editprof()
    {
        try {
            $tampung        =   request()->all();
            $user           =   User::where('personal_number',Auth::user()->personal_number)->first();
            $user->update($tampung);
            $cekuser        =   User::where('personal_number',Auth::user()->personal_number)->first();
            return response()->json([
                "status"    => '1',
                "data"      => $cekuser
            ]);
        } catch (\Throwable $th) {
            $data['message']    =   "Edit Profile Gagal";
            return response()->json([
                "status"    => '0',
                "data"      => $data
            ]);
        }
    }

    //get count lesson learned by tahap
    public function countLessonByTahap($stage="default"){
        try {
            // setting 12 bulan kebelakang
            $yesterday = date("Y-m-d", strtotime( '-0 days' ) );
            $month = date("Y-m-d", strtotime( '-6 months' ) );
            $urlFE = config('app.FE_url').'mylesson/';
            $data=[];

            $query = Lesson_learned::whereBetween("created_at", [$month, $yesterday])
                // ->without(['attach_file', 'project'])
                // ->join('projects', 'lesson_learneds.project_id', '=', 'projects.id')
                ->select(DB::raw("tahap, COUNT(tahap) as jml"))
                // ->where('projects.flag_mcs', 5)
                ->groupBy("tahap")
                ->get();

            foreach ($query as $key) {
                $object = new stdClass;
                $object->tahap = $key->tahap;
                $object->jml = $key->jml;
                $object->url = $urlFE.$key->tahap;
                $data[] = $object;
            }

            $out['data'] = $data;

            return response()->json([
                "status"    =>  1,
                "data" => $out
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    //get count initiative
    public function countInitiative($stage="default"){
        try {

            $urlFE = config('app.FE_url').'project';
            $query = CommunicationSupport::without(['attach_file', 'project'])
                ->join('projects', 'communication_support.project_id', '=', 'projects.id')
                ->select(DB::raw("projects.nama, sum(communication_support.views) as jml, 
                CONCAT_WS('/', '{$urlFE}', projects.slug) AS url"))
                ->where('projects.flag_mcs', 5)
                ->groupBy("projects.nama")
                ->groupBy("url")
                ->orderBy('jml', 'DESC')
                ->limit(5)
                ->get();

            $out['data'] = $query;

            return response()->json([
                "status"    =>  1,
                "data" => $out
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

//    get count com initiative
    public function countComInitiative($stage="default"){
        $type_list  = [
            "article" => "Articles",
            "logo" => "Icon, Logo, Maskot BRIVO",
            "infographics" => "Infographics",
            "transformation" => "Transformation Journey",
            "podcast" => "Podcast",
            "video" => "Video Content",
            "instagram" => "Instagram Content"];

        $data=[];
        $urlFE = config('app.FE_url').'mycomsupport/initiative';
        $vend = CommunicationSupport::without(['attach_file', 'project'])
            ->select(DB::raw("type_file, sum(views) as jml, 
                CONCAT_WS('/', '{$urlFE}', type_file) AS url"))
            ->where('status', 'publish')
            ->groupBy("type_file")
            ->groupBy("url")
            ->orderBy('jml', 'DESC')
            ->limit(5)
            ->get();

        foreach ($vend as $value) {
            $object = new stdClass;
            $object->type_file = $value->type_file? $type_list[$value->type_file]:'-';
            $object->jml = $value->jml;
            $object->url = $value->url;
            $data[] = $object;
        }

        $out['data'] = $data;

        return response()->json([
            "status"    =>  1,
            "data"      =>  $out
        ],200);
    }

    //count implementation
    public function countImplementation($stage = "default"){

        $urlFE = config('app.FE_url').'mycomsupport/implementation';
        $b = Implementation::without(['attach_file', 'project', 'project_managers', 'userchecker', 'usersigner', 'consultant', 'piloting', 'rollout', 'sosialisasi'])
            ->select(DB::raw("'Roll-Out' as tahap, coalesce(sum(views), 0) as jml, CONCAT_WS('/', '{$urlFE}', 'roll-out') AS url"))
            ->whereNotNull('desc_roll_out')
            ->groupBy("tahap")
            ->groupBy("url");

        $c = Implementation::without(['attach_file', 'project', 'project_managers', 'userchecker', 'usersigner', 'consultant', 'piloting', 'rollout', 'sosialisasi'])
            ->select(DB::raw("'Sosialisasi' as tahap, coalesce(sum(views), 0) as jml, CONCAT_WS('/', '{$urlFE}', 'sosialisasi') AS url"))
            ->whereNotNull('desc_sosialisasi')
            ->groupBy("tahap")
            ->groupBy("url");

        $data = Implementation::without(['attach_file', 'project', 'project_managers', 'userchecker', 'usersigner', 'consultant', 'piloting', 'rollout', 'sosialisasi'])
            ->select(DB::raw("'Piloting' as tahap, coalesce(sum(views), 0) as jml, CONCAT_WS('/', '{$urlFE}', 'piloting') AS url"))
            ->whereNotNull('desc_piloting')
            ->union($b)
            ->union($c)
            ->groupBy("tahap")
            ->groupBy("url")
            ->orderBy("jml", "desc")
            ->get();

        $out['data'] = $data;

        return response()->json([
            "status"    =>  1,
            "data"      =>  $out
        ],200);
    }
}
