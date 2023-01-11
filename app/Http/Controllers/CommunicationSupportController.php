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

    // clear filter
    public function getCommunicationInitiative(Request $request, $type)
    {
        try {
            $model = CommunicationSupport::with(['attach_file', 'favorite_com' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            }])->where('status',  'publish')->orderBy('created_at', 'desc');

            if ($type !== 'all') {
                $model->where('type_file', $type);
            }

            if ($request->get('sort')) {
                if ($type !== 'all') {
                    if ($request->get('sort') == 'title') {
                        $model = CommunicationSupport::with(['attach_file', 'favorite_com' => function ($q) {
                            $q->where('user_id', Auth::user()->id);
                        }])->where('type_file', $type)->where('status',  'publish')->orderBy('title', 'asc');
                    } elseif ($request->get('sort') == 'views') {
                        $model = CommunicationSupport::with(['attach_file', 'favorite_com' => function ($q) {
                            $q->where('user_id', Auth::user()->id);
                        }])->where('type_file', $type)->where('status',  'publish')->orderBy('views', 'desc');
                    } else {
                        $model = CommunicationSupport::with(['attach_file', 'favorite_com' => function ($q) {
                            $q->where('user_id', Auth::user()->id);
                        }])->where('type_file', $type)->where('status',  'publish')->orderBy('created_at', 'desc');
                    }
                } 
            }

            if ($request->get('search')) {
                $model->where('title', 'like', '%' . $request->get('search') . '%');
            }

            if ($request->get('year')) {
                $where_in_year = explode(",", $request->get('year'));
                $model->whereIn(DB::raw('year(tanggal_upload)'), $where_in_year);
            }

            if ($request->get('month')) {
                $where_in_month = explode(",", $request->get('month'));
                $model->whereIn(DB::raw('month(tanggal_upload)'), $where_in_month);
            }

            if ($request->get('divisi')) {
                $where_in = explode(",", $request->get('divisi'));
                $model->whereHas('project', function ($q) use ($where_in) {
                    $q->where('divisi_id', $where_in);
                });
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
                $model->whereHas('project', function ($q) use ($temp) {
                    $q->whereIn('divisi_id', $temp);
                });
            }

            $total = $model->get();
            $data = $model->paginate(12);

            $count = count(array($data));
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
            ], 200);
        } catch (\Throwable $th) {
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ], 200);
        }
    }

    public function getStrategic(Request $request)
    {
        try {
            $tahun      = $request->year;
            $bulan      = $request->month;
            $divisi     = $request->divisi;
            $sort       = $request->sort;
            $search     = $request->search;
            $tahap      = $request->tahap;

            $where_in = explode(",", $request->get('divisi'));
            $where_in_year = explode(",", $request->get('year'));
            $where_in_month = explode(",", $request->get('month'));

            $implementation = Implementation::join('projects', 'implementation.project_id', '=', 'projects.id')->where('status', 'publish')
                            ->select(DB::raw('distinct project_id'), DB::raw('projects.nama as nama, projects.slug as slug, projects.thumbnail, max(implementation.created_at) as created_at'))
                            ->groupBy('implementation.project_id')->where('status', 'publish');

            // $publish = fn ($q) => $q->where('status', 'publish');
            $publish = function ($q) use ($search) {
                $q->where('status', 'publish');
                $q->where('projects.nama', 'LIKE', '%' . $search . '%');
            };

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

                $query = Project::whereHas('communication_support', $publish)->orWhereHas('implementation', $publish)->whereIn('divisi_id', $temp)
                        ->orderBy('created_at', 'desc');
            }

            $query = Project::whereHas('communication_support', $publish)->orWhereHas('implementation', $publish)->orderBy('created_at', 'desc');

            if (!empty($sort)) {
                if ($sort == 'nama') {
                    $query->reorder('nama', 'asc');
                } elseif ($sort == 'created_at') {
                    $query->reorder('created_at', 'desc');
                } else {
                    $query->reorder('created_at', 'desc');
                }
            }

            if (!empty($search)) {
                $query->reorder('created_at', 'desc');
                // $query = Project::whereHas('communication_support', $cariProject)->orWhereHas('implementation', $cariProject)->orderBy('created_at', 'desc');
            }

            if (!empty($tahun)) {
                $query->whereIn(DB::raw('year(created_at)'), $where_in_year)->reorder('created_at', 'desc');
            }

            if (!empty($bulan)) {
                $query->whereIn(DB::raw('month(created_at)'), $where_in_month)->reorder('created_at', 'desc');
            }

            if (!empty($divisi)) {
                $query->whereIn('divisi_id', $where_in)->reorder('created_at', 'desc');
            }

            if (!empty($tahap)) {
                if ($tahap == 'piloting') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->whereNotNull('desc_piloting')->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->whereNotNull('desc_piloting')->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_piloting')->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->whereNotNull('desc_piloting')->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereNotNull('desc_piloting')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereNotNull('desc_piloting')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($divisi)) {
                        $query = $implementation->whereNotNull('desc_piloting')->whereIn('projects.divisi_id', $where_in)->orderBy('created_at', 'desc');
                    }

                    $query = $implementation->whereNotNull('desc_piloting')->orderBy('created_at', 'desc');
                } elseif ($tahap == 'roll-out') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->whereNotNull('desc_roll_out')->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->whereNotNull('desc_roll_out')->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_roll_out')->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($divisi)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->whereIn('projects.divisi_id', $where_in)->orderBy('created_at', 'desc');
                    }

                    $query = $implementation->whereNotNull('desc_roll_out')->orderBy('created_at', 'desc');
                } elseif ($tahap == 'sosialisasi') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($divisi)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->whereIn('projects.divisi_id', $where_in)->orderBy('created_at', 'desc');
                    }

                    $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('created_at', 'desc');
                } elseif ($tahap == 'all') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($divisi)) {
                        $query = $implementation->whereIn('projects.divisi_id', $where_in)->orderBy('created_at', 'desc');
                    }

                    $query = $implementation->orderBy('created_at', 'desc');
                }
            }

            $data = $query->get();
            $count = count($data);
            $countNotFilter = Project::whereHas('communication_support', function ($q) {
                $q->where('status', 'publish');
            })->count();

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data,
                "total"     => $count,
                "totalData" => $countNotFilter
            ], 200);
        } catch (\Throwable $th) {
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'error' => $th
            ], 200);
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
            $piloting = Implementation::where('project_id', $project->id)->where('status', 'publish')->whereNotNull('desc_piloting')->orderBy('created_at', 'desc')->take(5)->get();
            $rollOut = Implementation::where('project_id', $project->id)->where('status', 'publish')->whereNotNull('desc_roll_out')->orderBy('created_at', 'desc')->take(5)->get();
            $sosialisasi = Implementation::where('project_id', $project->id)->where('status', 'publish')->whereNotNull('desc_sosialisasi')->orderBy('created_at', 'desc')->take(5)->get();
            $data['piloting'] = $piloting;
	    $data['roll_out'] = $rollOut;
            $data['sosialisasi'] = $sosialisasi;

            $types = CommunicationSupport::where('project_id', $project->id)
                ->where('status', 'publish')->select('type_file')->distinct()->get();

            $model = CommunicationSupport::where('project_id', $project->id)
                ->where('status', 'publish');

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
                $datas_total = CommunicationSupport::where('project_id', $project->id)->where('status', 'publish')->where('type_file', $r->type_file)->count();
                $total = CommunicationSupport::where('project_id', $project->id)
                    ->where('status', 'publish')->where('type_file', $r->type_file)->count();

		/* if($request->get('search')) {
		    $datas = CommunicationSupport::where('project_id', $project->id)->where('status', 'publish')->where('type_file', $r->type_file)->where('title', 'like','%'.$request->get('search').'%')->get();
            	} */

		if($request->get('sort')) {
                    if ($request->get('sort') == 'title') {
                        $datas = CommunicationSupport::where('project_id', $project->id)->where('status', 'publish')->where('type_file', $r->type_file)->orderBy('title', 'asc')->take(5)->get();
		    } else {
		        $datas = CommunicationSupport::where('project_id', $project->id)->where('status', 'publish')->where('type_file', $r->type_file)->orderBy($request->get('sort'), 'desc')->take(5)->get();
		    }
            	}else {
		    $datas = CommunicationSupport::where('project_id', $project->id)->where('status', 'publish')->where('type_file', $r->type_file)->orderBy('created_at', 'desc')->take(5)->get();
		}

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

    // clear filter
    public function getAllImplementation(Request $request, $step)
    {
        $tahun      = $request->year;
        $month      = $request->month;
        $divisi     = $request->divisi;
        $sort       = $request->sort;
        $search     = $request->search;

        $where_in_year = explode(",", $tahun);
        $where_in_month = explode(",", $month);
        $where_in = explode(",", $divisi);

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

            $query = Implementation::with(['favorite_implementation' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            }])->join('projects', 'implementation.project_id', '=', 'projects.id')->where('status', 'publish')
                ->select(DB::raw('implementation.id, implementation.title, implementation.thumbnail, implementation.desc_piloting, 
            implementation.desc_roll_out, implementation.desc_sosialisasi, implementation.views, implementation.slug'))
                ->whereNotNull('desc_piloting')->whereIn('projects.divisi_id', $temp)->orderBy('implementation.created_at', 'desc');
        }

        $implementation = Implementation::with(['favorite_implementation' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            }])->join('projects', 'implementation.project_id', '=', 'projects.id')->where('status', 'publish')
            ->select(DB::raw('implementation.id, implementation.title, implementation.thumbnail, implementation.desc_piloting, 
            implementation.desc_roll_out, implementation.desc_sosialisasi, implementation.views, implementation.slug'));

        if ($step == 'piloting') {
            if (!empty($sort)) {
                if ($sort == 'title') {
                    $query = $implementation->whereNotNull('desc_piloting')->orderBy('implementation.title', 'asc');
                } elseif ($sort == 'views') {
                    $query = $implementation->whereNotNull('desc_piloting')->orderBy('implementation.views', 'desc');
                } else {
                    $query = $implementation->whereNotNull('desc_piloting')->orderBy('implementation.created_at', 'desc');
                }
            }
            if (!empty($tahun)) {
                $query = $implementation->whereNotNull('desc_piloting')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year);
            }
            if (!empty($month)) {
                $query = $implementation->whereNotNull('desc_piloting')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month); 
            }
            if (!empty($divisi)) {
                $query = $implementation->whereNotNull('desc_piloting')->whereIn('projects.divisi_id', $where_in);
            }
            if (!empty($search)) {
                $query = $implementation->whereNotNull('desc_piloting')->where('implementation.title', 'like', '%' . $search . '%');
            }
            $query = $implementation->whereNotNull('desc_piloting')->orderBy('implementation.created_at', 'desc');

        } else if ($step == 'roll-out') {
            if (!empty($sort)) {
                if ($sort == 'title') {
                    $query = $implementation->whereNotNull('desc_roll_out')->orderBy('implementation.title', 'asc');
                } elseif ($sort == 'views') {
                    $query = $implementation->whereNotNull('desc_roll_out')->orderBy('implementation.views', 'desc');
                } else {
                    $query = $implementation->whereNotNull('desc_roll_out')->orderBy('implementation.created_at', 'desc');
                }
            }
            if (!empty($tahun)) {
                $query = $implementation->whereNotNull('desc_roll_out')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year);
            } 
            if (!empty($month)) {
                $query = $implementation->whereNotNull('desc_roll_out')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month);
            } 
            if (!empty($divisi)) {
                $query = $implementation->whereNotNull('desc_roll_out')->whereIn('projects.divisi_id', $where_in);
            } 
            if (!empty($search)) {
                $query = $implementation->whereNotNull('desc_roll_out')->where('implementation.title', 'like', '%' . $search . '%');
            }
            $query = $implementation->whereNotNull('desc_roll_out')->orderBy('implementation.created_at', 'desc');

        } else if ($step == 'sosialisasi') {
            if (empty($tahun) && empty($month) && empty($divisi) && empty($sort) && empty($search)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('implementation.created_at', 'desc');
            }
            if (!empty($sort)) {
                if ($sort == 'title') {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('implementation.title', 'asc');
                } elseif ($sort == 'views') {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('implementation.views', 'desc');
                } else {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->orderBy('implementation.created_at', 'desc');
                }
            } 
            if (!empty($tahun)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year);
            }
            if (!empty($month)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month);
            }
            if (!empty($divisi)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->whereIn('projects.divisi_id', $where_in);
            }
            if (!empty($search)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->where('implementation.title', 'like', '%' . $search . '%');
            }
        } else {
            $datas['message']    =   'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas,
                'message'   => 'Tahap Implementasi tidak ditemukan'
            ], 200);
        }

        $total = $query->get();
        $data = $query->paginate(12);

        $count = count($data);
        $countTotal = count($total);
        $countNotFilter = $query->count();

        return response()->json([
            "message"   => "GET Berhasil",
            "status"    => 1,
            "data"      => $data,
            "totalRow"  => $count,
            "total"     => $countTotal,
            "totalData" => $countNotFilter
        ], 200);
    }

    public function getOneImplementation($slug) {
        try {
            $data = Implementation::with(['attach_file', 'consultant','project_managers', 'favorite_implementation' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            }])->where('status', 'publish')->where('slug', $slug)->first();

            $is_allowed = 0;
            if ($data->is_restricted == 1) {
                $user_access = explode(",",$data->user_access);
                foreach($user_access as $user){
                    if($user == Auth::user()->personal_number) {
                        $is_allowed = 1;
                        break;
                    }
                }
            } else {
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
                $data_error['error_code'] = 1;
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
                $data_error['error_code'] = 1;
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

 	    if ($datas['status'] == 'publish'){
           	$updateDetails['views'] = $datas->views + 1;
            	$model->update($updateDetails);
	    }

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
                $data_error['error_code'] = 1;
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
            $data_error['error_code'] = 1;
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
