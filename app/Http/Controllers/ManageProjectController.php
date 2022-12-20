<?php

namespace App\Http\Controllers;

use App\CommunicationSupport;
use App\Events\TriggerActivityEvent;
use App\Notifikasi;
use App\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class ManageProjectController extends Controller
{
    public $sort;

    public function reloadES(){
         //try {
             // remove index
             //$ch1 = curl_init();
             //$headers1  = [
             //            'Content-Type: application/json',
             //            'Accept: application/json',
             //        ];
             //curl_setopt($ch1, CURLOPT_URL,config('app.ES_url').'/*');
             //curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "DELETE");
             //curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
             //curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers1);
             //$result1     = curl_exec ($ch1);
             //$hasil1 = json_decode($result1);

             // remake index
             //Artisan::call('scout:import', ['model' => 'App\ConsultantES']);
             //Artisan::call('scout:import', ['model' => 'App\ProjectES']);
             //Artisan::call('scout:import', ['model' => 'App\DivisiES']);
             //Artisan::call('scout:import', ['model' => 'App\KeywordES']);
             //Artisan::call('scout:import', ['model' => 'App\ProjectManagerES']);

             //$ch = curl_init();
             //$headers  = [
             //            'Content-Type: application/json',
             //            'Accept: application/json',
             //        ];
             //$postData = [
             //    'properties' => [
             //                        'nama' => [
             //                            'type' => 'text',
             //                            "fielddata" => true
             //                        ]
             //                    ]
             //];
             //curl_setopt($ch, CURLOPT_URL,config('app.ES_url').'/project/_mapping');
             //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
             //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
             //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
             //$result     = curl_exec ($ch);
             //$hasil = json_decode($result);
         //}catch (\Throwable $th) {
         //}
    }

    public function review()
    {
        try {
            $q = Project::with('divisi','consultant')->whereIn('flag_mcs', [3,4,5,6])->get();
            $sync_es = Project::whereIn('flag_mcs', [3,4,5,6])->where('flag_es',NULL)->count();

            // siapkan response
            $data['message'] = 'GET Berhasil.';
            $data['bahan']   = $q;
            $data['sync_es'] = $sync_es;

            // update notifikasi
            $terbaru['status_read'] =   1;
            $queryproject   = Project::without(['project_managers','divisi','keywords', 'consultant','lesson_learned','comment','usermaker','userchecker','usersigner'])->where(function($q){
                $q->orWhere('status_read', null);
                $q->orWhere('status_read', 0);
            })->whereIn('flag_mcs', [3,4,5])->update($terbaru);

            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function review_content($fil_div="*",$fil_kon="*",$search="*")
    {
        // return response()->json(['test' => 'masuk']);
        $fil_div    =   urldecode($fil_div);
        $fil_kon    =   urldecode($fil_kon);
        $search     = str_replace("_"," ",$search);

        if ($fil_div == "*") {
            $divisi     =   [];
        }else{
            $divisi     =   explode(',', $fil_div)??[];
            $divisi     =   $divisi ==  ["*"]    ?  $divisi=[] : $divisi;
        }
        if ($fil_kon == "*") {
            $consultant =   [];
        }else{
            $consultant =   explode(',', $fil_kon)??[];
            $consultant =   $consultant ==  ["*"]    ?  $consultant=[] : $consultant;
        }

        if ($search == "*") {
            if ($divisi == [] && $consultant == []) {
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->whereIn('flag_mcs', [3,4,5,6])->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }elseif ($divisi <> [] && $consultant == []) {
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->whereIn('divisis.divisi',$divisi)->whereIn('flag_mcs', [3,4,5,6])->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }elseif ($divisi == [] && $consultant <> []) {
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->join('consultant_projects', 'consultant_projects.project_id', '=', 'projects.id')->join('consultants', 'consultants.id', '=', 'consultant_projects.consultant_id')->whereIn('consultants.nama',$consultant)->whereIn('flag_mcs', [3,4,5,6])->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }else{
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->join('consultant_projects', 'consultant_projects.project_id', '=', 'projects.id')->join('consultants', 'consultants.id', '=', 'consultant_projects.consultant_id')->whereIn('consultants.nama',$consultant)->whereIn('divisis.divisi',$divisi)->whereIn('flag_mcs', [3,4,5,6])->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }
        }else{
            if ($divisi == [] && $consultant == []) {
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->whereIn('flag_mcs', [3,4,5,6])->where('projects.nama','like','%'.$search.'%')->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }elseif ($divisi <> [] && $consultant == []) {
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->whereIn('divisis.divisi',$divisi)->whereIn('flag_mcs', [3,4,5,6])->where('projects.nama','like','%'.$search.'%')->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }elseif ($divisi == [] && $consultant <> []) {
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->join('consultant_projects', 'consultant_projects.project_id', '=', 'projects.id')->join('consultants', 'consultants.id', '=', 'consultant_projects.consultant_id')->whereIn('consultants.nama',$consultant)->whereIn('flag_mcs', [3,4,5,6])->where('projects.nama','like','%'.$search.'%')->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }else{
                $testing    = Project::join('divisis', 'divisis.id', '=', 'projects.divisi_id')->join('consultant_projects', 'consultant_projects.project_id', '=', 'projects.id')->join('consultants', 'consultants.id', '=', 'consultant_projects.consultant_id')->whereIn('consultants.nama',$consultant)->whereIn('divisis.divisi',$divisi)->where('projects.nama','like','%'.$search.'%')->whereIn('flag_mcs', [3,4,5,6])->select('divisis.divisi as divisi','projects.nama','direktorat','tanggal_mulai','flag_mcs','is_restricted','projects.id','review_at','publish_at','checker_at','signer_at','projects.created_at','slug','status_read')->orderBy('created_at', 'DESC')->groupBy('id');
                $data       = $testing->paginate(10);
            }
        }

        // return response()->json($data);
        $view       = view('manage_project.list',compact('data'))->render();
        $paginate   = view('manage_user.paginate',compact('data'))->render();
        $modal      = view('manage_project.modal',compact('data'))->render();
        // $cek        = $data;

        Project::whereIn('flag_mcs', [3,4,5])->where('status_read', null)->update(['status_read'=>1]);
        try {
            $res['message'] = 'GET Berhasil.';
            $res['html'] = $view;
            $res['paginate'] = $paginate;
            $res['modal'] = $modal;
            // $res['cek'] = $cek;
            return response()->json([
                "status"    => 1,
                "data"      => $res,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function rekomendasi()
    {
        $q = Project::with('divisi','consultant')->where('flag_mcs', 5)->where('is_recomended', 1)->get();
        $qq = Project::with('divisi','consultant')->where('flag_mcs', 5)->where('is_recomended', 0)->get();

        try {
            $data['message'] = 'GET Berhasil.';
            $data['data'] = $q;
            $data['data_not_recomended'] = $qq;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'GET Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    // public function all()
    // {
    //     $q = Project::where('is_recomended', 0)->get();

    //     try {
    //         $data['message'] = 'GET Berhasil.';
    //         $data['data'] = $q;
    //         return response()->json([
    //             "status"    => 1,
    //             "data"      => $data,
    //         ],200);
    //     } catch (\Throwable $th) {
    //         $data['message'] = 'GET Gagal';
    //         return response()->json([
    //             'status'    =>  0,
    //             'data'      =>  $data
    //         ],200);
    //     }
    // }

    public function add($id)
    {
        $q = Project::find($id);
        $up['is_recomended'] = 1;

        try {
            $q->update($up);
            $data['message']    =   'Merekomendasikan Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Merekomendasikan Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function passing($tipe)
    {
        $checks = request('checks');

        if ($tipe == 'pemilik') {
            $q = Project::whereIn('flag_mcs', [3, 4])
            ->orWhereIn('divisi_id', $checks) //pemilik proyek
            ->get();

            // $q = Project::with(['divisi'=> function ($a) use($checks){
            //     $a->whereIn('id', $checks);
            // }])
            // ->whereIn('flag_mcs', [3, 4])
            // ->get();

            // return $q;

        } elseif ($tipe == 'nama') {
            $q = Project::with(['consultant'=> function ($a) use($checks){
                $a->whereIn('id', $checks);
            }])
            ->whereIn('flag_mcs', [3, 4])
            ->get();
        }

        try {
            $data['message']    =   'Passing Berhasil.';
            $data['data']       =   $q;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);

        } catch (\Throwable $th) {
            $data['message']    =   'Passing Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function hapus_rekomendasi($id)
    {
        $q = Project::find($id);
        $up['is_recomended'] = 0;

        try {
            $q->update($up);
            $data['message']    =   'Delete Rekomendasi Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Delete Rekomendasi Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function publish($id)
    {
        try {
            // cek project
            if (Auth::User()->role == 0 || Auth::User()->role == 1 || Auth::User()->role == 2) {
                $data_error['message'] = 'Cannot Access This Feature';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }else{
                $query = Project::find($id);
            }

            // pengecheckan
            if (isset($query->nama)) {
                $sekarang = Carbon::now();
                // if ($query->review_at == null) {
                //     $up['review_at']   = Carbon::now();
                // }
                // $up['publish_at']   = Carbon::now();
                // $up['flag_mcs']     = 5;
                // $up['flag_es']      = NULL;

                // $query->update($up);

                if ($query->review_at == null) {
                  DB::select(" update projects set `review_at` = '$sekarang', `publish_at` = '$sekarang', `flag_mcs` = 5, `flag_es` = NULL where id = '$id' ");
                }else{
                  DB::select(" update projects set `publish_at` = '$sekarang', `flag_mcs` = 5, `flag_es` = NULL where id = '$id' ");
                }
            }

            $Notifikasi         = Notifikasi::create([
                'user_id'      =>  $query->user_maker,
                'kategori'     =>  1,
                'judul'        =>  'Proyek Telah Terpublish',
                'pesan'        =>  "Proyek Anda Dengan Nama <b>".$query->nama."</b> Telah Di <b class='text-info'>Publish</b> Oleh <b>Admin</b>",
                'direct'       =>  config('app.FE_url')."myproject",
                'status'       =>  0,
            ]);

            // Event Activity
            event(new TriggerActivityEvent(2, $query->user_maker));

            // refresh ES
            //$this->reloadES();

            $data['message']    =   'Publish Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Publish Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function unpublish($id)
    {
        try {
            // $query = Project::find($id);
            // $up['flag_mcs']   = 6; //STATUS UNPUBLISHED (BY ADMIN)
            // $up['flag_es']    = NULL;

            // $query->update($up);

            DB::select(" update projects set `flag_mcs` = 6, `flag_es` = NULL where id = '$id' ");

            // refresh ES
            //$this->reloadES();

            $data['message']    =   'Unpublish Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Unpublish Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function hapus($id)
    {
        try {
            $query = Project::find($id);
            $query->delete();

            DB::table('projects')->update(array('flag_es' => NULL));

            $cek = CommunicationSupport::where('project_id', $id)->get();
            if (!is_null($cek)) {
                $sekarang = Carbon::now();
                CommunicationSupport::where('project_id', $id)->update(['status' => 'deleted', 'deleted_at' => $sekarang,
                    'deleted_by' => Auth::User()->personal_number, 'updated_by' => Auth::User()->personal_number]);
            }

            // refresh ES
            //$this->reloadES();
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

    public function sort($by)
    {
        $query = Project::where('flag_mcs', 3)
            ->orWhere('flag_mcs', 4)
            ->get();

        $tampungQuery = [];
        foreach ($query as $q) {
            $object = new stdClass();
            $object = $q;
            $object->nama_divisi = $q->divisi->divisi; //naro objectnya didalem
            $tampungQuery[] = $object;
        }

        // ------------------
        if ($by == 'NAZ') {
            $result = collect($tampungQuery)->sortBy('nama');
        } elseif ($by == 'NZA') {
            $result = collect($tampungQuery)->sortByDesc('nama');
        }

        if ($by == 'PAZ') {
            $result = collect($tampungQuery)->sortBy('nama_divisi');
        } elseif ($by == 'PZA') {
            $result = collect($tampungQuery)->sortByDesc('nama_divisi');
        }

        try {
            $data['message']    =   'Sort Berhasil.';
            $data['data']       =   $result;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Sort Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function sortpass($by) //untested
    {
        $query = Project::whereIn('flag_mcs', [3, 4])
            ->get();

        $tampungQuery = [];
        foreach ($query as $q) {
            $object = new stdClass();
            $object = $q;
            $object->nama_divisi = $q->divisi->divisi; //naro objectnya didalem
            $object->nama_konsultan = $q->consultant->nama; //naro objectnya didalem
            $tampungQuery[] = $object;
        }

        if ($by == 'k') {
            $result = collect($tampungQuery)->sortBy('nama_konsultan');
        } elseif ($by == 'd') {
            $result = collect($tampungQuery)->sortByDesc('nama_divisi');
        }

        try {
            $data['message']    =   'Sort Berhasil.';
            $data['data']       =   $result;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Sort Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }
}
