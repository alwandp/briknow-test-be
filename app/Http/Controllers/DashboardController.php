<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\Activity;
use App\AttachFile;
use App\CommunicationSupport;
use App\ConsultantProject;
use App\Divisi;
use App\Forum;
use App\Implementation;
use App\Keywords;
use App\Level;
use App\Project;
use App\Search_log;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use stdClass;
use Embed\Embed;

class DashboardController extends Controller
{
    public function getVisitor()
    {
        try {
            $today = date('Y-m-d', strtotime(now()));
            $last30d = date('Y-m-d', strtotime('-30 days'));
            $last24h = date('Y-m-d');

            $q1 = DB::table('search_logs')
            ->join('projects', 'projects.id', '=', 'search_logs.project_id')
            ->where('flag_mcs', 5)
            ->where('search_logs.created_at',">=", $last30d." 00:00:01")
            ->where('search_logs.created_at',"<=", $today." 23:59:59")
            ->count();

            $q2 = DB::table('search_logs')
            ->join('projects', 'projects.id', '=', 'search_logs.project_id')
            ->where('flag_mcs', 5)
            ->where('search_logs.created_at','>=',$last24h." 00:00:01")
            ->where('search_logs.created_at',"<=", $last24h." 23:59:59")
            ->count();

            // // most user
            $qMostUsUk = DB::table('search_logs')
            ->join('users', 'users.id', '=', 'search_logs.user_id')
            ->join('projects', 'projects.id', '=', 'search_logs.project_id')
            ->select('user_id',DB::raw('count(*) as total'))
            ->where('flag_mcs', 5)
            ->groupBy('user_id')
            ->orderBy('total','desc')
            ->limit(10)
            ->get();

            $qMostUk = DB::table('search_logs')
            ->join('users', 'users.id', '=', 'search_logs.user_id')
            ->join('divisis', 'divisis.id', '=', 'users.divisi')
            ->join('projects', 'projects.id', '=', 'search_logs.project_id')
            ->select('divisis.shortname',DB::raw('count(*) as total'))
            ->where('flag_mcs', 5)
            ->groupBy('divisis.shortname')
            ->orderBy('total','desc')
            ->limit(10)
            ->get();

            $data['message'] = 'Get Data Success';
            $data['today'] = $q2;
            $data['last30'] = $q1;
            $data['most'] = $qMostUsUk;
            $data['mostUK'] = $qMostUk;

            return response()->json([
                "status"    =>  1,
                "data"      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getProjectList()
    {
        $query = Project::withCount(['search_log'])->orderBy('search_log_count', 'DESC')->where('flag_mcs', 5)->limit(5)->get();
        try {
            $data['message'] = 'Get Data Success';
            $data['data'] = $query;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getalldatagrafik()
    {
        // try {
            $today = carbon::now();
            $last30d = date('Y-m-d', strtotime('-30 days'));
            $last24h = date('Y-m-d', strtotime('-24 hours'));

            // project visitor
            $data=[];
            $data =DB::table('search_logs')
            ->join('projects', 'projects.id', '=', 'search_logs.project_id')
            ->whereBetween('search_logs.created_at', [$last30d, $today])
            ->select(DB::raw('DATE_FORMAT(search_logs.created_at, "%Y-%m-%d") date'),DB::raw('count(*) as jumlahpengunjung'), DB::raw('SUM(projects.flag_mcs) as flag_mcs'))
            ->where('flag_mcs', 5)
            ->groupBy('date')
            ->orderBy('date','asc')
            ->get();

            // projecct consultant
            $querycon =DB::table('consultant_projects')
            ->join('consultants', 'consultants.id', '=', 'consultant_projects.consultant_id')
            ->join('projects', 'projects.id', '=', 'consultant_projects.project_id')
            ->select('consultant_projects.consultant_id as url', 'consultants.nama as nama_consultant' ,DB::raw('count(*) as jumlah_project'), DB::raw('SUM(projects.flag_mcs) as flag_mcs'))
            ->where('flag_mcs', 5)
            ->groupBy('url')
            ->orderBy('jumlah_project','desc')
            ->get();

            $querycon->map(function ($key, $value) {
                $key->url =  isset($key->url) ? config('app.FE_url').'consultant/'.$key->url : config('app.FE_url').'dashboard/'.'#';
                $key->nama_consultant =  $key->nama_consultant;
            });

            //project divisi
            $querydiv =DB::table('divisis')
            ->join('projects', 'projects.divisi_id', '=', 'divisis.id')
            ->select('divisis.direktorat as nama_direktorat','divisis.divisi as nama_divisi', 'divisis.shortname as short', 'divisis.id as url' ,DB::raw('count(*) as jumlah_project'), DB::raw('SUM(projects.flag_mcs) as flag_mcs'))
            ->where('flag_mcs', 5)
            ->groupBy('url')
            ->orderBy('jumlah_project','desc')
            ->get();

            $querydiv->map(function ($key, $value) {
                $key->url =  isset($key->url) ? config('app.FE_url').'divisi/'.$key->url : config('app.FE_url').'dashboard/'.'#';
                $key->nama_divisi =  $key->nama_divisi;
            });

            // project tahun
            $querythn =DB::table('projects')
            ->select(DB::raw('DATE_FORMAT(tanggal_mulai, "%Y") tahun'),DB::raw('count(*) as jumlah_project'))
            ->where('flag_mcs', 5)
            ->groupBy('tahun')
            ->orderBy('tahun','asc')
            ->get();

            // prepare parse
            $out['visitor']     = $data;
            $out['consultant']  = $querycon;
            $out['divisi']      = $querydiv;
            $out['tahun']       = $querythn;

            return response()->json([
                "status"    =>  1,
                "data"      =>  $out,
            ],200);
        // } catch (\Throwable $th) {
        //     $data['message']    =   'Something Went Wrong';
        //     return response()->json([
        //         "status"    =>  0,
        //         "data"      =>  $data
        //     ],200);
        // }
    }

    public function getProjectVisitor()
    {
        $data=[];
        $today = carbon::now();
        $last30d = date('Y-m-d', strtotime('-30 days'));
        $last24h = date('Y-m-d', strtotime('-24 hours'));

        $data =DB::table('search_logs')
        ->whereBetween('created_at', [$last30d, $today])
        ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") date'),DB::raw('count(*) as jumlahpengunjung'))
        ->groupBy('date')
        ->orderBy('date','asc')
        ->get();

        $out['data'] = $data;
        try {
            return response()->json([
                "status"    =>  1,
                "data"      =>  $out,
                // "v_today"   =>  count($q1),
                // "v_last30"  =>  count($q2),
                // "v_total"   =>  count($q3)
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getProjectKonsultan()
    {
        try {
            $query =DB::table('consultant_projects')
            ->join('consultants', 'consultants.id', '=', 'consultant_projects.consultant_id')
            ->select('consultant_projects.consultant_id as url', 'consultants.nama as nama_consultant' ,DB::raw('count(*) as jumlah_project'))
            ->groupBy('url')
            ->orderBy('jumlah_project','desc')
            ->get();

            $query->map(function ($key, $value) {
                $key->url =  isset($key->url) ? config('app.FE_url').'consultant/'.$key->url : config('app.FE_url').'dashboard/'.'#';
                $key->nama_consultant =  \Str::limit($key->nama_consultant, 15,'..');
            });


            $out['data'] = $query;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $out,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getProjectDivisi()
    {
        try {
            $query =DB::table('divisis')
            ->join('projects', 'projects.divisi_id', '=', 'divisis.id')
            ->select('divisis.direktorat as nama_direktorat','divisis.divisi as nama_divisi', 'divisis.id as url' ,DB::raw('count(*) as jumlah_project'))
            ->groupBy('url')
            ->orderBy('jumlah_project','desc')
            ->get();

            $query->map(function ($key, $value) {
                $key->url =  isset($key->url) ? config('app.FE_url').'divisi/'.$key->url : config('app.FE_url').'dashboard/'.'#';
                $key->nama_divisi =  \Str::limit($key->nama_divisi, 15,'..');
            });

            $out['data'] = $query;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $out,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getProjectTahun()
    {
        try {
            $query =DB::table('projects')
            ->select(DB::raw('DATE_FORMAT(tanggal_mulai, "%Y") tahun'),DB::raw('count(*) as jumlah_project'))
            ->where('flag_mcs', 5)
            ->groupBy('tahun')
            ->orderBy('tahun','asc')
            ->get();

            $out['data'] = $query; //harus di sort disini (masih urutannya blom bener)

            $data['message'] = 'Get Data Success';
            $data['data'] = $query;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $out,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getProjectPerTahun()
    {
        $data=[];
        $yearsBefore = date('Y-m-d', strtotime('-6 years'));
        $today = date('Y-m-d', strtotime(now()));

        $query = project::where('flag_mcs', 5)->whereBetween('tanggal_mulai', [$yearsBefore." 00:00:01", $today." 23:59:59"])->get();
        // return response()->json($query);

        $out['data'] = $query; //harus di sort disini (masih urutannya blom bener)

        try {
            $data['message']    = 'Get Data Success';
            $data['data']       = $out;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function getProjectTags()
    {
        //new
        // ->select('nama')->groupBy('nama')
        $query = Keywords::with(['project' => function($q){
            $q->where('flag_mcs',5);
        }])->select('nama')->groupBy('nama')->withCount('keyword_log')->orderby('keyword_log_count','desc')->limit(10)->get();

        $namas = [];
        foreach ($query as $q) {
            $namas[] = $q->nama;
        }

        // $query2 = Keywords::with(['project' => function($q) use($namas) {
        //     $q->where('flag_mcs',5);
        // }])->whereIn('nama', $namas)->get();

        $query2 = Keywords::rightjoin('projects', 'projects.id', '=', 'keywords.project_id')->where('projects.flag_mcs',5)->whereIn('keywords.nama', $namas)->get(['keywords.nama','projects.nama AS nama_project','projects.slug']);

        $gabung = [
            'tags' => $query,
            'relateds' => $query2,
        ];

        try {
            $data['message'] = 'Get Data Success';
            $data['data'] = $gabung;
            return response()->json([
                "status"    =>  1,
                "data"      =>  $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Something Went Wrong';
            return response()->json([
                "status"    =>  0,
                "data"      =>  $data
            ],200);
        }
    }

    public function dashboardInitiative() {
        $type_list  = [
            "article"           => "Articles",
            "logo"              => "Icon, Logo, Maskot BRIVO",
            "infographics"      => "Infographics",
            "transformation"    => "Transformation Journey",
            "podcast"           => "Podcast",
            "video"             => "Video Content",
            "instagram"         => "Instagram Content"];

        $types = CommunicationSupport::select(DB::raw('type_file, SUM(views) as total'))->groupBy('type_file')
            ->orderby('total','desc')->get();
        $data = [];
        foreach($types as $key => $value){
            $sum = CommunicationSupport::without(['attach_file', 'project'])->where('type_file', $value->type_file)->where('status', '!=', 'deleted')
                ->select(DB::raw('SUM(views) as total'))->first();
            $views = CommunicationSupport::without(['attach_file', 'project'])
                ->select(DB::raw('id, title, views, thumbnail, slug'))
                ->where('type_file', $value->type_file)->where('status', '!=', 'deleted')
                ->orderby('views','desc')->take(5)->get();
            $download = CommunicationSupport::without(['attach_file', 'project'])
                ->select(DB::raw('id, title, downloads, thumbnail, slug'))
                ->where('type_file', $value->type_file)->where('status', '!=', 'deleted')
                ->orderby('downloads','desc')->take(5)->get();

            $data[$key]['tipe']             = $value->type_file;
            $data[$key]['tipe_nama']        = $type_list[$value->type_file];
            $data[$key]['views_most']       = $views;
            $data[$key]['downloads_most']   = $download;
            $data[$key]['search']           = $sum->total;
        }

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $data,
        ],200);
    }

    public function dashboardStrategic() {
        $type_list  = [
            "article"           => "Articles",
            "logo"              => "Icon, Logo, Maskot BRIVO",
            "infographics"      => "Infographics",
            "transformation"    => "Transformation Journey",
            "podcast"           => "Podcast",
            "video"             => "Video Content",
            "instagram"         => "Instagram Content"];
        $project = Project::whereHas('communication_support')
            ->without(['project_managers', 'divisi', 'keywords', 'consultant', 'lesson_learned', 'comment',
                'usermaker', 'userchecker', 'usersigner', 'communication_support', 'implementation'])
            ->select('id', 'nama', 'thumbnail', 'slug')->get();

        $data = [];
        foreach ($project as $keys=>$values) {
            $types = CommunicationSupport::select(DB::raw('type_file, SUM(views) as total'))
                ->where('project_id', $values->id)
                ->groupBy('type_file')
                ->orderby('total','desc')->get();

            $search_total = 0;
            $rowspan = 0;
            $strategic = [];
            foreach($types as $key => $value){
                $sum = CommunicationSupport::without(['attach_file', 'project'])->where('project_id', $values->id)
                    ->where('type_file', $value->type_file)->where('status', '!=', 'deleted')
                    ->select(DB::raw('SUM(views) as total'))->first();
                $views = CommunicationSupport::without(['attach_file', 'project'])->where('project_id', $values->id)
                    ->select(DB::raw('id, title, views, thumbnail, slug'))
                    ->where('type_file', $value->type_file)->where('status', '!=', 'deleted')
                    ->orderby('views','desc')->take(5)->get();
                $download = CommunicationSupport::without(['attach_file', 'project'])->where('project_id', $values->id)
                    ->select(DB::raw('id, title, downloads, thumbnail, slug'))
                    ->where('type_file', $value->type_file)->where('status', '!=', 'deleted')
                    ->orderby('downloads','desc')->take(5)->get();

                $strategic[$key]['tipe']             = $value->type_file;
                $strategic[$key]['tipe_nama']        = $type_list[$value->type_file];
                $strategic[$key]['views_most']       = $views;
                $strategic[$key]['downloads_most']   = $download;
                $strategic[$key]['search']           = $sum->total;

                $search_total += $sum->total;
                $rowspan += count($views);
            }

            $data[$keys]['id'] = $values->id;
            $data[$keys]['nama'] = $values->nama;
            $data[$keys]['thumbnail'] = $values->thumbnail;
            $data[$keys]['slug'] = $values->slug;
            $data[$keys]['search_total'] = $search_total;
            $data[$keys]['strategic'] = $strategic;
            $data[$keys]['rowspan'] = $rowspan;
        }

        usort($data, function($a, $b) { return $b['search_total'] <=> $a['search_total']; });

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $data,
        ],200);
    }

    public function dashboardImplementation() {
        $type_list  = [
            "piloting"           => "Piloting",
            "rollout"            => "Roll-Out",
            "sosialisasi"        => "Sosialisasi"];
        $step = AttachFile::whereNotNull('implementation_id')->select('tipe')->distinct()->get();

        $data = [];
        foreach($step as $key => $value){
            $sum = Implementation::without(['attach_file', 'project', 'project_managers', 'userchecker', 'usersigner', 'consultant', 'piloting', 'rollout', 'sosialisasi'])
                ->whereHas($value->tipe)->where('status', '!=', 'deleted')
                ->select(DB::raw('SUM(views) as total'))->first();
            $views = Implementation::without(['attach_file', 'project', 'project_managers', 'userchecker', 'usersigner', 'consultant', 'piloting', 'rollout', 'sosialisasi'])
                ->join('projects', 'projects.id', '=', 'implementation.project_id')
                ->select(DB::raw('implementation.id as implementation_id, implementation.title, implementation.views,
                 projects.id as project_id, projects.nama as nama_project, projects.thumbnail, projects.slug'))
                ->whereHas($value->tipe)->where('implementation.status', '!=', 'deleted')
                ->orderby('implementation.views','desc')->take(5)->get();

            $data[$key]['tipe']         = $value->tipe;
            $data[$key]['tipe_nama']    = $type_list[$value->tipe];
            $data[$key]['search_most']  = $views;
            $data[$key]['search_total'] = $sum->total;
        }

        usort($data, function($a, $b) { return $b['search_total'] <=> $a['search_total']; });

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $data,
        ],200);
    }

    public function managegamification()
    {
        $qUser  = User::where('personal_number', Auth::user()->personal_number)->latest()->first();
        $qAc    = Achievement::get();
        $qLv    = Level::get();
        $qAct   = Activity::get();

        $tmp['user'] = $qUser;
        $tmp['achievements'] = $qAc;
        $tmp['levels'] = $qLv;
        $tmp['activities'] = $qAct;

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

    public function gamificationsave_level()
    {
        $tmp_id                 =   request()->id;
        $tmp_level_name         =   request()->level_name;
        $tmp_level_xp           =   request()->level_xp;
        if (isset($tmp_id)) {
            $i=0;
	    $has      = 0;
	    $tamp_has = 0;

	    foreach ($tmp_id as $id) {
		if($tmp_level_xp[$i] >= $tamp_has){
		  $tamp_has = $tmp_level_xp[$i];
		}else{
		  $has = 1;
		}
		$i++;
            }

	    if($has == 1){
		$datas['message']    =   'Requirement Exp Harus Terurut';
            	return response()->json([
                	'status'    =>  0,
                	'data'      =>  $datas
            	],200);
	     }

	    $i=0;
            foreach ($tmp_id as $id) {
                $level       = Level::find($id);

                $level->name = $tmp_level_name[$i];
                $level->xp   = $tmp_level_xp[$i];

                $level->save();
                $i++;
            }
        }

        try {
            $data['message']        =   'Save Level Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'Save Level Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function gamificationsave_activity()
    {
        $tmp_id                    =   request()->id;
        $tmp_activity_name         =   request()->activity_name;
        $tmp_activity_xp           =   request()->activity_xp;
        if (isset($tmp_id)) {
            $i=0;
            foreach ($tmp_id as $id) {
                $level = Activity::find($id);
                $level->name = $tmp_activity_name[$i];
                $level->xp = $tmp_activity_xp[$i];
                $level->save();
                $i++;
            }
        }

        try {
            $data['message']        =   'Save Activity Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'Save Activity Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function gamificationsave_achievement()
    {
        $tmp_id                 =   request()->id;
        $tmp_achievement_name   =   request()->name;
        $tmp_achievement_value  =   request()->value;
        if (isset($tmp_id)) {
            $i=0;
            foreach ($tmp_id as $id) {
                $level = Achievement::find($id);
                $level->name = $tmp_achievement_name[$i];
                $level->value = $tmp_achievement_value[$i];
                $level->save();
                $i++;
            }
        }

        try {
            $data['message']        =   'Save Achievement Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'Save Achievement Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function manageforum_all()
    {
        $query = Forum::with(['user', 'forumcomment'])->get();
        //belum bisa save ini ngambil dari atas

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $query;
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

    public function manageforum_removed()
    {
        $query = Forum::with(['user', 'forumcomment'])->
        where('status', 2)->get();
        //belum bisa save ini ngambil dari atas

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $query;
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

    public function adminforumsave()
    {
        $query = Forum::with(['user', 'forumcomment'])->get();
        //belum bisa save ini ngambil dari atas

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $query;
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

    public function listall($search = '*'){
        $search = str_replace("_"," ",$search);
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }else{
            $data = Forum::with(['user', 'forumUser' => function($q){
                $q->where('user_id',Auth::user()->personal_number);
            }])
            ->where(function($q) use ($search){
                $q->where('judul','like','%'.$search.'%');
                $q->orWhere('content','like','%'.$search.'%');
            })
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }

        $datas = [];
        foreach ($data as $item) {
            if($item->kategori == 1){
                // $embed = new Embed();
                // $info = $embed->get($item->content);
                // $previewlink = $info->image;
                // $link        = $info->url;
                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = config('app.FE_url').'assets/img/default-forum.png';
                $link        = '';
            }

            // $tbt = Carbon::create($item->created_at)->format('d F Y');
            // $waktu = Carbon::create($item->created_at)->timezone('Asia/Jakarta')->format('H:i');

            $object = new stdClass;
            $object->id         = $item->id;
            $object->link       = $link;
            $object->thumbnail  = $previewlink;
            $object->kategori   = $item->kategori;
            $object->username   = $item->user->name;
            $object->updated_at = $item->updated_at;
            $object->restriction= $item->restriction;
            $object->slug       = $item->slug;
            $object->judul      = $item->judul;
            $object->content    = $item->content;
            $object->forumcomment= $item->forumcomment;
            $object->status     = $item->status;
            // $object->created_at = $tbt." at ".$waktu;
            $datas[]            =   $object;


        }

        $view       = view('Manageforum.list',compact('datas'))->render();
        $paginate   = view('Manageforum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            return response()->json([
                "status"    => 1,
                "data"      => $resp,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function listremoved($search = '*'){
        $search = str_replace("_"," ",$search);
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where('status', '2')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }else{
            $data = Forum::with(['user', 'forumUser' => function($q){
                $q->where('user_id',Auth::user()->personal_number);
            }])
            ->where(function($q) use ($search){
                $q->where('judul','like','%'.$search.'%');
                $q->orWhere('content','like','%'.$search.'%');
            })
            ->where('status', '2')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }

        $datas = [];
        foreach ($data as $item) {
            if($item->kategori == 1){
                // $embed = new Embed();
                // $info = $embed->get($item->content);
                // $previewlink = $info->image;
                // $link        = $info->url;
                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = config('app.FE_url').'assets/img/default-forum.png';
                $link        = '';
            }

            // $tbt = Carbon::create($item->created_at)->format('d F Y');
            // $waktu = Carbon::create($item->created_at)->timezone('Asia/Jakarta')->format('H:i');

            $object = new stdClass;
            $object->id       = $item->id;
            $object->link       = $link;
            $object->thumbnail  = $previewlink;
            $object->kategori   = $item->kategori;
            $object->username   = $item->user->name;
            $object->updated_at = $item->updated_at;
            $object->restriction= $item->restriction;
            $object->slug       = $item->slug;
            $object->judul      = $item->judul;
            $object->content    = $item->content;
            $object->forumcomment= $item->forumcomment;
            $object->status     = $item->status;
            // $object->created_at = $tbt." at ".$waktu;
            $datas[]            =   $object;


        }

        $view       = view('Manageforum.list',compact('datas'))->render();
        $paginate   = view('Manageforum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            return response()->json([
                "status"    => 1,
                "data"      => $resp,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function remove($id)
    {
        $query = Forum::find($id);
        $query->status = 2;

        try {
            $query->save();
            $data['message']    =   'Delete Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Delete Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function restore($id)
    {
        $query = Forum::find($id);
        $query->status = 1;

        try {
            $query->save();
            $data['message']    =   'Restore Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Restore Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function sortPublic($search = '*'){
        $search = str_replace("_"," ",$search);
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where(function($q){
                $q->where('restriction',0);//cek apakah public
            })
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }else{
            $data = Forum::with(['user', 'forumUser' => function($q){
                $q->where('user_id',Auth::user()->personal_number);
            }])
            ->where(function($q) use ($search){
                $q->where('judul','like','%'.$search.'%');
                $q->orWhere('content','like','%'.$search.'%');
            })
            ->where(function($q){
                $q->where('restriction',0);//cek apakah public
            })
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }

        $datas = [];
        foreach ($data as $item) {
            if($item->kategori == 1){
                // $embed = new Embed();
                // $info = $embed->get($item->content);
                // $previewlink = $info->image;
                // $link        = $info->url;

                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = config('app.FE_url').'assets/img/default-forum.png';
                $link        = "";
            }

            // $tbt = Carbon::create($item->created_at)->format('d F Y');
            // $waktu = Carbon::create($item->created_at)->timezone('Asia/Jakarta')->format('H:i');

            $object = new stdClass;
            $object->id         = $item->id;
            $object->link       = $link;
            $object->thumbnail  = $previewlink;
            $object->kategori   = $item->kategori;
            $object->username   = $item->user->name;
            $object->updated_at = $item->updated_at;
            $object->restriction= $item->restriction;
            $object->slug       = $item->slug;
            $object->judul      = $item->judul;
            $object->content    = $item->content;
            $object->forumcomment= $item->forumcomment;
            $object->status     = $item->status;
            // $object->created_at = $tbt." at ".$waktu;
            $datas[]            =   $object;


        }

        $view       = view('Manageforum.list',compact('datas'))->render();
        $paginate   = view('Manageforum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            return response()->json([
                "status"    => 1,
                "data"      => $resp,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function sortPrivate($search = '*'){
        $search = str_replace("_"," ",$search);
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where(function($q){
                $q->where('restriction',1);//cek apakah public
            })
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }else{
            $data = Forum::with(['user', 'forumUser' => function($q){
                $q->where('user_id',Auth::user()->personal_number);
            }])
            ->where(function($q) use ($search){
                $q->where('judul','like','%'.$search.'%');
                $q->orWhere('content','like','%'.$search.'%');
            })
            ->where(function($q){
                $q->where('restriction',1);//cek apakah public
            })
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }

        $datas = [];
        foreach ($data as $item) {
            if($item->kategori == 1){
                // $embed = new Embed();
                // $info = $embed->get($item->content);
                // $previewlink = $info->image;
                // $link        = $info->url;

                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = config('app.FE_url').'assets/img/default-forum.png';
                $link        = "";
            }

            // $tbt = Carbon::create($item->created_at)->format('d F Y');
            // $waktu = Carbon::create($item->created_at)->timezone('Asia/Jakarta')->format('H:i');

            $object = new stdClass;
            $object->id         = $item->id;
            $object->link       = $link;
            $object->thumbnail  = $previewlink;
            $object->kategori   = $item->kategori;
            $object->username   = $item->user->name;
            $object->updated_at = $item->updated_at;
            $object->restriction= $item->restriction;
            $object->slug       = $item->slug;
            $object->judul      = $item->judul;
            $object->content    = $item->content;
            $object->forumcomment= $item->forumcomment;
            $object->status     = $item->status;
            // $object->created_at = $tbt." at ".$waktu;
            $datas[]            =   $object;


        }

        $view       = view('Manageforum.list',compact('datas'))->render();
        $paginate   = view('Manageforum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            return response()->json([
                "status"    => 1,
                "data"      => $resp,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function sortPublicRemoved($search = '*'){
        $search = str_replace("_"," ",$search);
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where(function($q){
                $q->where('restriction',0);//cek apakah public
            })
            ->where('status', '2')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }else{
            $data = Forum::with(['user', 'forumUser' => function($q){
                $q->where('user_id',Auth::user()->personal_number);
            }])
            ->where(function($q) use ($search){
                $q->where('judul','like','%'.$search.'%');
                $q->orWhere('content','like','%'.$search.'%');
            })
            ->where(function($q){
                $q->where('restriction',0);//cek apakah public
            })
            ->where('status', '2')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }

        $datas = [];
        foreach ($data as $item) {
            if($item->kategori == 1){
                // $embed = new Embed();
                // $info = $embed->get($item->content);
                // $previewlink = $info->image;
                // $link        = $info->url;

                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = config('app.FE_url').'assets/img/default-forum.png';
                $link        = "";
            }

            // $tbt = Carbon::create($item->created_at)->format('d F Y');
            // $waktu = Carbon::create($item->created_at)->timezone('Asia/Jakarta')->format('H:i');

            $object = new stdClass;
            $object->id         = $item->id;
            $object->link       = $link;
            $object->thumbnail  = $previewlink;
            $object->kategori   = $item->kategori;
            $object->username   = $item->user->name;
            $object->updated_at = $item->updated_at;
            $object->restriction= $item->restriction;
            $object->slug       = $item->slug;
            $object->judul      = $item->judul;
            $object->content    = $item->content;
            $object->forumcomment= $item->forumcomment;
            $object->status     = $item->status;
            // $object->created_at = $tbt." at ".$waktu;
            $datas[]            =   $object;


        }

        $view       = view('Manageforum.list',compact('datas'))->render();
        $paginate   = view('Manageforum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            return response()->json([
                "status"    => 1,
                "data"      => $resp,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function sortPrivateRemoved($search = '*'){
        $search = str_replace("_"," ",$search);
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where(function($q){
                $q->where('restriction',1);//cek apakah public
            })
            ->where('status', '2')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }else{
            $data = Forum::with(['user', 'forumUser' => function($q){
                $q->where('user_id',Auth::user()->personal_number);
            }])
            ->where(function($q) use ($search){
                $q->where('judul','like','%'.$search.'%');
                $q->orWhere('content','like','%'.$search.'%');
            })
            ->where(function($q){
                $q->where('restriction',1);//cek apakah public
            })
            ->where('status', '2')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }

        $datas = [];
        foreach ($data as $item) {
            if($item->kategori == 1){
                // $embed = new Embed();
                // $info = $embed->get($item->content);
                // $previewlink = $info->image;
                // $link        = $info->url;

                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = config('app.FE_url').'assets/img/default-forum.png';
                $link        = "";
            }

            // $tbt = Carbon::create($item->created_at)->format('d F Y');
            // $waktu = Carbon::create($item->created_at)->timezone('Asia/Jakarta')->format('H:i');

            $object = new stdClass;
            $object->id         = $item->id;
            $object->link       = $link;
            $object->thumbnail  = $previewlink;
            $object->kategori   = $item->kategori;
            $object->username   = $item->user->name;
            $object->updated_at = $item->updated_at;
            $object->restriction= $item->restriction;
            $object->slug       = $item->slug;
            $object->judul      = $item->judul;
            $object->content    = $item->content;
            $object->forumcomment= $item->forumcomment;
            $object->status     = $item->status;
            // $object->created_at = $tbt." at ".$waktu;
            $datas[]            =   $object;


        }

        $view       = view('Manageforum.list',compact('datas'))->render();
        $paginate   = view('Manageforum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            return response()->json([
                "status"    => 1,
                "data"      => $resp,
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
