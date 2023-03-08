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
                $div = $request->get('divisi');
                if ($div !== 'init') {
                    $model->whereHas('project', function ($q) use ($div) {
                        $q->where('divisi_id', $div);
                    });
                } else {
                    $model->whereHas('project');
                }
            }

            if ($request->get('direktorat')) {
                $dir = $request->get('direktorat');
                if ($dir == 'general') {
                    $model->where('project_id', null);
                } else if ($dir == 'init') {
                    $model->whereHas('project', function ($q) {
                        $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                        $q->where('divisis.direktorat', 0);
                    });
                } else {
                    $model->whereHas('project', function ($q) use ($dir) {
                        $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                        $q->where('divisis.direktorat', $dir);
                    });
                }
            }

            $data = $model->paginate(9);
            $count = count($data);

            $paginate   = view('cominit_public.paginate',compact('data'))->render();
            $data['paginate'] = $paginate;
            $data['total'] = $count;

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data
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
            $dir        = $request->direktorat;
            $sort       = $request->sort;
            $search     = $request->search;
            $tahap      = $request->tahap;

            $where_in = explode(",", $request->get('divisi'));
            $where_in_year = explode(",", $request->get('year'));
            $where_in_month = explode(",", $request->get('month'));

            $implementation = Implementation::join('projects', 'implementation.project_id', '=', 'projects.id')->join('divisis', 'projects.divisi_id', '=', 'divisis.id')->where('status', 'publish')
                            ->select(DB::raw('distinct project_id'), DB::raw('projects.nama as nama, projects.slug as slug, projects.thumbnail, max(implementation.created_at) as created_at, divisis.direktorat as direktorat'))
                            ->groupBy('implementation.project_id')->where('status', 'publish');

            // $publish = fn ($q) => $q->where('status', 'publish');
            $publish = function ($q) use ($search) {
                $q->where('status', 'publish');
                $q->where('projects.nama', 'LIKE', '%' . $search . '%');
            };

            $query = Project::whereHas('communication_support', $publish)->orWhereHas('implementation', $publish)->orderBy('projects.created_at', 'desc');

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
            }

            if (!empty($tahun)) {
                $query->whereIn(DB::raw('year(created_at)'), $where_in_year)->reorder('created_at', 'desc');
            }

            if (!empty($bulan)) {
                $query->whereIn(DB::raw('month(created_at)'), $where_in_month)->reorder('created_at', 'desc');
            }

            if (!empty($dir)) {
                if ($dir !== 'init') {
                    $query->where('direktorat', $dir);
                    $query = Project::whereHas('communication_support', function ($q) use ($dir) {
                            $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                            $q->where('direktorat', $dir);
                            $q->where('communication_support.status', 'publish');
                        })->orWhereHas('implementation', function ($q) use ($dir) {
                            $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                            $q->where('direktorat', $dir);
                            $q->where('implementation.status', 'publish');
                        });
                    $query->reorder('created_at', 'desc');

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
                        $query->where('nama', 'LIKE', '%' . $search . '%');
                    }
        
                    if (!empty($tahun)) {
                        $query->whereIn(DB::raw('year(created_at)'), $where_in_year)->reorder('created_at', 'desc');
                    }
        
                    if (!empty($bulan)) {
                        $query->whereIn(DB::raw('month(created_at)'), $where_in_month)->reorder('created_at', 'desc');
                    }
                } else {
                    $query = Project::whereHas('communication_support', function ($q) {
                        $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                        $q->where('direktorat', 0);
                        $q->where('communication_support.status', 'publish');
                    })->orWhereHas('implementation', function ($q) {
                        $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                        $q->where('direktorat', 0);
                        $q->where('implementation.status', 'publish');
                    });
                    $query->reorder('created_at', 'desc');

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
                        $query->where('nama', 'LIKE', '%' . $search . '%');
                    }
        
                    if (!empty($tahun)) {
                        $query->whereIn(DB::raw('year(created_at)'), $where_in_year)->reorder('created_at', 'desc');
                    }
        
                    if (!empty($bulan)) {
                        $query->whereIn(DB::raw('month(created_at)'), $where_in_month)->reorder('created_at', 'desc');
                    }
                }
            }

            if (!empty($divisi)) {
                if ($divisi !== 'init') {
                    $query = Project::whereHas('communication_support', function ($q) use ($divisi) {
                        $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                        $q->where('divisi_id', $divisi);
                        $q->where('communication_support.status', 'publish');
                    })->orWhereHas('implementation', function ($q) use ($divisi) {
                        $q->join('divisis', 'projects.divisi_id', '=', 'divisis.id');
                        $q->where('divisi_id', $divisi);
                        $q->where('implementation.status', 'publish');
                    });
                    $query->reorder('created_at', 'desc');

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
                        $query->where('nama', 'LIKE', '%' . $search . '%');
                    }
        
                    if (!empty($tahun)) {
                        $query->whereIn(DB::raw('year(created_at)'), $where_in_year)->reorder('created_at', 'desc');
                    }
        
                    if (!empty($bulan)) {
                        $query->whereIn(DB::raw('month(created_at)'), $where_in_month)->reorder('created_at', 'desc');
                    }
                } else {
                    $query = Project::whereHas('communication_support', function ($q) {
                        $q->where('communication_support.status', 'publish');
                    })->orWhereHas('implementation', function ($q) {
                        $q->where('implementation.status', 'publish');
                    });
                    $query->reorder('created_at', 'desc');

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
                        $query->where('nama', 'LIKE', '%' . $search . '%');
                    }
        
                    if (!empty($tahun)) {
                        $query->whereIn(DB::raw('year(created_at)'), $where_in_year)->reorder('created_at', 'desc');
                    }
        
                    if (!empty($bulan)) {
                        $query->whereIn(DB::raw('month(created_at)'), $where_in_month)->reorder('created_at', 'desc');
                    }
                }
            }

            if (!empty($tahap)) {
                if ($tahap == 'piloting') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($dir)) {
                        if ($dir !== 'init') {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('direktorat', $dir)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('direktorat', 0)->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($divisi)) {
                        if ($divisi !== 'init') {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('projects.divisi_id', $divisi)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('created_at', 'desc');
                        }
                    }

                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('created_at', 'desc');
                } elseif ($tahap == 'roll-out') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($dir)) {
                        if ($dir !== 'init') {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('direktorat', $dir)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('direktorat', 0)->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($divisi)) {
                        if ($divisi !== 'init') {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('projects.divisi_id', $divisi)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('created_at', 'desc');
                        }
                    }

                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('created_at', 'desc');
                } elseif ($tahap == 'sosialisasi') {
                    if (!empty($sort)) {
                        if ($sort == 'nama') {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('nama', 'asc');
                        } elseif ($sort == 'created_at') {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($search)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('nama', 'LIKE', '%' . $search . '%');
                    }

                    if (!empty($tahun)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year)->orderBy('created_at', 'desc');
                    }

                    if (!empty($bulan)) {
                        $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month)->orderBy('created_at', 'desc');
                    }

                    if (!empty($dir)) {
                        if ($dir !== 'init') {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('direktorat', $dir)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('direktorat', 0)->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($divisi)) {
                        if ($divisi !== 'init') {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('projects.divisi_id', $divisi)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('created_at', 'desc');
                        }
                    }

                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('created_at', 'desc');
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

                    if (!empty($dir)) {
                        if ($dir !== 'init') {
                            $query = $implementation->where('direktorat', $dir)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->where('direktorat', 0)->orderBy('created_at', 'desc');
                        }
                    }

                    if (!empty($divisi)) {
                        if ($divisi !== 'init') {
                            $query = $implementation->where('projects.divisi_id', $divisi)->orderBy('created_at', 'desc');
                        } else {
                            $query = $implementation->orderBy('created_at', 'desc');
                        }
                    }

                    $query = $implementation->orderBy('created_at', 'desc');
                }
            }

            $data = $query->paginate(9);
            $count = count($data);

            $paginate   = view('strategic_public.paginate',compact('data'))->render();
            $data['paginate'] = $paginate;
            $data['total'] = $count;

            return response()->json([
                "message"   => "GET Berhasil",
                "status"    => 1,
                "data"      => $data
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
            $piloting = Implementation::where('project_id', $project->id)->where('status', 'publish')->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('created_at', 'desc')->take(5)->get();
            $rollOut = Implementation::where('project_id', $project->id)->where('status', 'publish')->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('created_at', 'desc')->take(5)->get();
            $sosialisasi = Implementation::where('project_id', $project->id)->where('status', 'publish')->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('created_at', 'desc')->take(5)->get();
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
        $dir        = $request->direktorat;
        $divisi     = $request->divisi;
        $sort       = $request->sort;
        $search     = $request->search;

        $where_in_year = explode(",", $tahun);
        $where_in_month = explode(",", $month);
        $where_in = explode(",", $divisi);

        $implementation = Implementation::with(['favorite_implementation' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            }])->join('projects', 'implementation.project_id', '=', 'projects.id')->join('divisis', 'projects.divisi_id', '=', 'divisis.id')->where('status', 'publish')
            ->select(DB::raw('implementation.id, implementation.title, implementation.thumbnail, implementation.desc_piloting, 
            implementation.desc_roll_out, implementation.desc_sosialisasi, implementation.views, implementation.slug, divisis.direktorat'));

        if ($step == 'piloting') {
            if (!empty($sort)) {
                if ($sort == 'title') {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('implementation.title', 'asc');
                } elseif ($sort == 'views') {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('implementation.views', 'desc');
                } else {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('implementation.created_at', 'desc');
                }
            }
            if (!empty($tahun)) {
                $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year);
            }
            if (!empty($month)) {
                $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month); 
            }
            if (!empty($dir)) {
                if ($dir !== 'init') {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('divisis.direktorat', $dir);
                } else {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('divisis.direktorat', 0);
                }
            }
            if (!empty($divisi)) {
                if ($divisi !== 'init') {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('projects.divisi_id', $divisi);
                } else {
                    $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish');
                }
            }
            // if (!empty($divisi)) {
            //     $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->whereIn('projects.divisi_id', $where_in);
            // }
            if (!empty($search)) {
                $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->where('implementation.title', 'like', '%' . $search . '%');
            }
            $query = $implementation->whereNotNull('desc_piloting')->where('status_piloting','publish')->orderBy('implementation.created_at', 'desc');

        } else if ($step == 'roll-out') {
            if (!empty($sort)) {
                if ($sort == 'title') {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('implementation.title', 'asc');
                } elseif ($sort == 'views') {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('implementation.views', 'desc');
                } else {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('implementation.created_at', 'desc');
                }
            }
            if (!empty($tahun)) {
                $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year);
            } 
            if (!empty($month)) {
                $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month);
            } 
            if (!empty($dir)) {
                if ($dir !== 'init') {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('divisis.direktorat', $dir);
                } else {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('divisis.direktorat', 0);
                }
            }
            if (!empty($divisi)) {
                if ($divisi !== 'init') {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('projects.divisi_id', $divisi);
                } else {
                    $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish');
                }
            }
            if (!empty($search)) {
                $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->where('implementation.title', 'like', '%' . $search . '%');
            }
            $query = $implementation->whereNotNull('desc_roll_out')->where('status_roll_out','publish')->orderBy('implementation.created_at', 'desc');

        } else if ($step == 'sosialisasi') {
            if (empty($tahun) && empty($month) && empty($divisi) && empty($sort) && empty($search)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('implementation.created_at', 'desc');
            }
            if (!empty($sort)) {
                if ($sort == 'title') {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('implementation.title', 'asc');
                } elseif ($sort == 'views') {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('implementation.views', 'desc');
                } else {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->orderBy('implementation.created_at', 'desc');
                }
            } 
            if (!empty($tahun)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->whereIn(DB::raw('year(implementation.tanggal_mulai)'), $where_in_year);
            }
            if (!empty($month)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->whereIn(DB::raw('month(implementation.tanggal_mulai)'), $where_in_month);
            }
            if (!empty($dir)) {
                if ($dir !== 'init') {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('divisis.direktorat', $dir);
                } else {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('divisis.direktorat', 0);
                }
            }
            if (!empty($divisi)) {
                if ($divisi !== 'init') {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('projects.divisi_id', $divisi);
                } else {
                    $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish');
                }
            }
            if (!empty($search)) {
                $query = $implementation->whereNotNull('desc_sosialisasi')->where('status_sosialisasi','publish')->where('implementation.title', 'like', '%' . $search . '%');
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
        $data = $query->paginate(2);

        $count = count($data);
        $countTotal = count($total);
        $countNotFilter = $query->count();

        $paginate   = view('implementation.paginate',compact('data'))->render();
        $data['paginate'] = $paginate;
        $data['total'] = $count;

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
            }])->where('status', 'publish')->where('slug', $slug)->without('achievement','userchecker','usersigner',)->first();

            // $implementationCheck = Implementation::where('slug', $slug)->first();
            // if($implementationCheck->status_piloting == 'unpublish'){
            //     $data->makeHidden('desc_piloting');
            // }
            // if($implementationCheck->status_roll_out == 'unpublish'){
            //     $data->makeHidden('desc_roll_out');
            // }
            // if($implementationCheck->status_sosialisasi == 'unpublish'){
            //     $data->makeHidden('desc_sosialisasi');
            // }

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
        $model = Project::find($id);
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
