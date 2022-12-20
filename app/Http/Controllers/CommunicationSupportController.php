<?php

namespace App\Http\Controllers;

use App\CommunicationSupport;
use App\Divisi;
use App\Implementation;
use App\Project;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommunicationSupportController extends Controller {

    public function getCommunicationInitiative(Request $request, $type) {
        try {
            $model = CommunicationSupport::with(['attach_file', 'favorite_com' => function($q) {
                $q->where('user_id', Auth::user()->id);
            }])->where('status',  'publish');

            if ($type !== 'all'){
                $model->where('type_file', $type);
            }

            $order = 'desc';
            if($request->get('order')) {
                $order = $request->get('order');
            }
            $sort= 'views';
            $model->orderBy($sort, $order);
            if($request->get('sort')) {
                $sort = $request->get('sort');
                $model->orderBy($sort, $order);
            }
            if($request->get('search')) {
                $model->where('title', 'like','%'.$request->get('search').'%');
            }
            if($request->get('year')) {
                $where_in_year = explode(",",$request->get('year'));
                $model->whereIn(DB::raw('year(created_at)'), $where_in_year);
            }
            if($request->get('month')) {
                $where_in_month = explode(",",$request->get('month'));
                $model->whereIn(DB::raw('month(created_at)'), $where_in_month);
            }
            if($request->get('divisi')) {
                $where_in = explode(",",$request->get('divisi'));
                $model->whereHas('project', function ($q) use ($where_in) {
                    $q->where('divisi_id', $where_in);
                });
            }

            if($request->get('direktorat')) {
                $dir = $request->get('direktorat');
                $dir = str_replace('-', ' ', $dir);
                $dir = str_replace('%20', ' ', $dir);


                if ($dir === 'NULL') {
                    $queryDiv = Divisi::where('direktorat', NULL)->get();
                } else {
                    $queryDiv = Divisi::where('direktorat', 'like', '%'.$dir.'%')->get();
                }

                $temp=[];
                foreach ($queryDiv as $itemDiv) {
                    $temp[] = $itemDiv->id;
                }
                $model->whereHas('project', function ($q) use ($temp) {
                    $q->whereIn('divisi_id', $temp);
                });
            }

            $total = $model->get();
            $data = $model->paginate(12);

            $count = count($data);
            $countTotal = count($total);
            $countNotFilter = CommunicationSupport::with(['attach_file'])
                ->where('type_file', $type)->where('status',  'publish')->count();

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
                "totalRow"  => $count,
                "total"     => $countTotal,
                "totalData" => $countNotFilter
            ],200);
        } catch (\Throwable $th){
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ],200);
        }

    }

    public function getStrategic(Request $request) {
        try {
            $model = Project::whereHas('communication_support', function($q) {
                $q->where('status', 'publish');
            });

            $order = 'asc';
            if($request->get('order')) {
                $order = $request->get('order');
            }
            if($request->get('sort')) {
                $model->orderBy($request->get('sort'), $order);
            }
            if($request->get('search')) {
                $model->where('projects.nama', 'like','%'.$request->get('search').'%');
            }
            if($request->get('year')) {
                $where_in_year = explode(",",$request->get('year'));
                $model->whereIn(DB::raw('year(created_at)'), $where_in_year);
            }
            if($request->get('month')) {
                $where_in_month = explode(",",$request->get('month'));
                $model->whereIn(DB::raw('month(created_at)'), $where_in_month);
            }
            if($request->get('divisi')) {
                $where_in = explode(",",$request->get('divisi'));
                $model->whereIn('divisi_id', $where_in);
            }
            if($request->get('tahap')) {
                if ($request->get('tahap') == 'piloting') {
                    $model->whereHas('implementation', function ($q) {
                        $q->whereNotNull('desc_piloting');
                    });
                } else if ($request->get('tahap') == 'roll-out') {
                    $model->whereHas('implementation', function ($q) {
                        $q->whereNotNull('desc_roll_out');
                    });
                } else if ($request->get('tahap') == 'sosialisasi') {
                    $model->whereHas('implementation', function ($q) {
                        $q->whereNotNull('desc_sosialisasi');
                    });
                }
            }
            if($request->get('direktorat')) {
                $dir = $request->get('direktorat');
                $dir = str_replace('-', ' ', $dir);
                $dir = str_replace('%20', ' ', $dir);


                if ($dir === 'NULL') {
                    $queryDiv = Divisi::where('direktorat', NULL)->get();
                } else {
                    $queryDiv = Divisi::where('direktorat', 'like', '%'.$dir.'%')->get();
                }

                $temp=[];
                foreach ($queryDiv as $itemDiv) {
                    $temp[] = $itemDiv->id;
                }
                $model->whereIn('divisi_id', $temp);;
            }

            $data = $model->get();

            $count = count($data);
            $countNotFilter = Project::whereHas('communication_support', function($q) {
                $q->where('status', 'publish');
            })->count();

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
                "total"     => $count,
                "totalData" => $countNotFilter
            ],200);
        } catch (\Throwable $th){
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ],200);
        }
    }

    public function getStrategicByProject(Request $request, $slug) {
        try {
            $type_list = [
                ["id" => "article", "name" => "Articles"],
                ["id" => "logo", "name" => "Icon, Logo, Maskot BRIVO"],
                ["id" => "infographics", "name" => "Infographics"],
                ["id" => "transformation", "name" => "Transformation Journey"],
                ["id" => "podcast", "name" => "Podcast"],
                ["id" => "video", "name" => "Video Content"],
                ["id" => "instagram", "name" => "Instagram Content"],
            ];
            $project        = Project::where('slug',$slug)->first();
            if (!$project) {
                $data_error['message'] = 'Proyek tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }
            $data['project'] = $project;
            $types = CommunicationSupport::where('project_id', $project->id)
                ->where('status', 'publish')->select('type_file')->distinct()->get();

            $model = CommunicationSupport::where('project_id', $project->id)
                ->where('status', 'publish');
            $order = 'asc';
            if($request->get('order')) {
                $order = $request->get('order');
            }
            if($request->get('sort')) {
                $model->orderBy($request->get('sort'), $order);
            }
            if($request->get('search')) {
                $model->where('title', 'like','%'.$request->get('search').'%');
            }
            if($request->get('year')) {
                $where_in_year = explode(",",$request->get('year'));
                $model->whereIn(DB::raw('year(created_at)'), $where_in_year);
            }
            if($request->get('month')) {
                $where_in_month = explode(",",$request->get('month'));
                $model->whereIn(DB::raw('month(created_at)'), $where_in_month);
            }
            if($request->get('divisi')) {
                $where_in = explode(",",$request->get('divisi'));
                $model->whereHas('project', function ($q) use ($where_in) {
                    $q->whereIn('divisi_id', $where_in);
                });
            }

            $type_file = [];
            foreach($types as $r){
                $key = array_search($r->type_file, array_column($type_list, 'id'));
                $datas = $model->where('type_file', $r->type_file)->take(5)->get();
                $datas_total = $model->where('type_file', $r->type_file)->count();
                $total = CommunicationSupport::where('project_id', $project->id)
                    ->where('status', '!=', 'deleted')->where('type_file', $r->type_file)->count();

                $type_list[$key]['data'] = $datas;
                $type_list[$key]['total_data'] = $datas_total;
                $type_list[$key]['total_notFiltered'] = $total;
                $type_file[] = $type_list[$key];
            }
            $data['content'] = $type_file;

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th){
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ],200);
        }
    }

    public function getStrategicByProjectAndType(Request $request, $slug, $type) {
        try {
            $project        = Project::where('slug',$slug)->first();
            if (!$project) {
                $data_error['message'] = 'Proyek tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }
            $model = CommunicationSupport::with(['attach_file', 'favorite_com' => function($q) {
                $q->where('user_id', Auth::user()->id);
            }])
                ->where('communication_support.type_file', $type)
                ->where('project_id', $project->id)
                ->where('communication_support.status', 'publish');

            $order = 'asc';
            if($request->get('order')) {
                $order = $request->get('order');
            }
            if($request->get('sort')) {
                $model->orderBy($request->get('sort'), $order);
            }
            if($request->get('search')) {
                $model->where('title', 'like','%'.$request->get('search').'%');
            }
            if($request->get('year')) {
                $where_in_year = explode(",",$request->get('year'));
                $model->whereIn(DB::raw('year(created_at)'), $where_in_year);
            }
            if($request->get('month')) {
                $where_in_month = explode(",",$request->get('month'));
                $model->whereIn(DB::raw('month(created_at)'), $where_in_month);
            }
            if($request->get('divisi')) {
                $where_in = explode(",",$request->get('divisi'));
                $model->whereHas('project', function ($q) use ($where_in) {
                    $q->whereIn('divisi_id', $where_in);
                });
            }

            $data = $model->get();

            $count = count($data);
            $countNotFilter = CommunicationSupport::with(['attach_file'])
                ->where('communication_support.type_file', $type)
                ->where('project_id', $project->id)
                ->where('communication_support.status', 'publish')->count();

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
                "total"     => $count,
                "totalData" => $countNotFilter
            ],200);
        } catch (\Throwable $th){
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ],200);
        }
    }

    public function getAllImplementation(Request $request, $step) {

            $model = Implementation::with(['favorite_implementation' => function ($q) {
                    $q->where('user_id', Auth::user()->id);
                }])
                ->join('projects', 'implementation.project_id', '=', 'projects.id')
                ->select(DB::raw('implementation.id, implementation.title, implementation.thumbnail, implementation.desc_piloting, 
                implementation.desc_roll_out, implementation.desc_sosialisasi, implementation.views, implementation.slug'))
                ->where('implementation.status', 'publish');
            $modelCount = Implementation::where('status', 'publish');

            if ($step == 'piloting') {
                $model->whereNotNull('desc_piloting');
                $modelCount->whereNotNull('desc_piloting');
            } else if ($step == 'roll-out') {
                $model->whereNotNull('desc_roll_out');
                $modelCount->whereNotNull('desc_roll_out');
            } else if ($step == 'sosialisasi') {
                $model->whereNotNull('desc_sosialisasi');
                $modelCount->whereNotNull('desc_sosialisasi');
            } else {
                $datas['message']    =   'GET Gagal';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $datas,
                    'message'   => 'Tahap Implementasi tidak ditemukan'
                ],200);
            }

            $order = 'asc';
            if($request->get('order')) {
                $order = $request->get('order');
            }
            if($request->get('sort')) {
                $model->orderBy('implementation.created_at', $order);
            }
            if($request->get('search')) {
                $model->where('implementation.title', 'like','%'.$request->get('search').'%');
            }
            if($request->get('year')) {
                $where_in_year = explode(",",$request->get('year'));
                $model->whereIn(DB::raw('year(implementation.created_at)'), $where_in_year);
            }
            if($request->get('month')) {
                $where_in_month = explode(",",$request->get('month'));
                $model->whereIn(DB::raw('month(implementation.created_at)'), $where_in_month);
            }
            if($request->get('divisi')) {
                $where_in = explode(",",$request->get('divisi'));
                $model->whereIn('projects.divisi_id', $where_in);
            }

            if ($request->get('direktorat')) {
                $dir = $request->get('direktorat');
                $dir = str_replace('-', ' ', $dir);
                $dir = str_replace('%20', ' ', $dir);


                if ($dir === 'NULL') {
                    $queryDiv = Divisi::where('direktorat', NULL)->get();
                } else {
                    $queryDiv = Divisi::where('direktorat', 'like', '%' . $dir . '%')->get();
                }

                $temp = [];
                foreach ($queryDiv as $itemDiv) {
                    $temp[] = $itemDiv->id;
                }
                $model->whereIn('projects.divisi_id', $temp);;
            }

            $total = $model->get();
            $data = $model->paginate(5);

            $count = count($data);
            $countTotal = count($total);
            $countNotFilter = $modelCount->count();

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
                "totalRow"  => $count,
                "total"     => $countTotal,
                "totalData" => $countNotFilter
            ],200);

    }

    public function getOneImplementation($slug) {
        try {
            $data = Implementation::with(['attach_file', 'consultant','project_managers', 'favorite_implementation' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            }])->where('status', 'publish')->where('slug', $slug)->first();

            $is_allowed = 0; //dilarang
            if ($data->is_restricted == 1) {
                $user_access = explode(",",$data->user_access);
                foreach($user_access as $user){
                    if($user == Auth::user()->personal_number) { //ketika yg di allow match dengan yg lagi login maka..
                        $is_allowed = 1; //diizinkan
                        break;
                    }
                }
            } else { //jika tidak bersifat RESTRICTED maka siapa saja yg akses dengan user apapun bisa lolos liat
                $is_allowed = 1;
            }

            if (count($data->favorite_implementation) > 0) {
                $data['favorite'] = 1;
            } else {
                $data['favorite'] = 0;
            }
            $data['is_allowed']     =   $is_allowed??0;

            if (!$data) {
                $data_error['message'] = 'Implementation tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th){
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ],200);
        }
    }

    function viewContent($table, $id) {
        try {
            $model = null;
            if ($table == 'implementation') {
                $model = Implementation::where('id', $id);
            } else if ($table == 'content') {
                $model = CommunicationSupport::where('id', $id);
            }

            $datas = $model->first();
            if (!$datas) {
                $data_error['message'] = 'Proyek tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            $updateDetails['views'] = $datas->views + 1;
            $model->update($updateDetails);

            $data_upd = $model->first();

            return response()->json([
                "status"    => 1,
                "data"      => $data_upd,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Update gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data,
                'error'     => $th
            ],200);
        }

    }

    function getFile($content, $id) {
        try {
            $model = null;
            if ($content == 'implementation') {
                $model = Implementation::where('id', $id);
            } else if ($content == 'content') {
                $model = CommunicationSupport::where('id', $id);
            }

            $datas = $model->first();
            if (!$datas) {
                $data_error['message'] = 'Proyek tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            $updateDetails['downloads'] = $datas->downloads + 1;
            $model->update($updateDetails);

            $data = $model->first();

            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Update gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data,
                'error'     => $th
            ],200);
        }
    }

    function getFileProject($id) {

        $model = Project::find($id)->first();
        if (!$model) {
            $data_error['message'] = 'Proyek tidak ditemukan!';
            $data_error['error_code'] = 1; //error
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 400);
        }

        return response()->json([
            "status"    => 1,
            "data"      => $model,
        ],200);
    }

}
