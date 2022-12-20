<?php

namespace App\Http\Controllers;

use App\Consultant;
use App\ConsultantProject;
use App\Divisi;
use App\Document;
use App\Events\TriggerActivityEvent;
use App\Keywords;
use App\Lesson_learned;
use App\Notifikasi;
use App\Project;
use App\Project_managers;
use App\Restriction;
use App\TempUpload;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Log;
use Validator;

class KontribusiController extends Controller
{
    public function reloadES(){
        // try {
        //     // remove index
        //     $ch1 = curl_init();
        //     $headers1  = [
        //                 'Content-Type: application/json',
        //                 'Accept: application/json',
        //             ];
        //     curl_setopt($ch1, CURLOPT_URL,config('app.ES_url').'/*');
        //     curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "DELETE");
        //     curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers1);
        //     $result1     = curl_exec ($ch1);
        //     $hasil1 = json_decode($result1);

        //     // remake index
        //     Artisan::call('scout:import', ['model' => 'App\Project']);
        //     Artisan::call('scout:import', ['model' => 'App\Consultant']);
        //     Artisan::call('scout:import', ['model' => 'App\Divisi']);
        //     Artisan::call('scout:import', ['model' => 'App\Keywords']);
        //     Artisan::call('scout:import', ['model' => 'App\Project_managers']);

        //     $ch = curl_init();
        //     $headers  = [
        //                 'Content-Type: application/json',
        //                 'Accept: application/json',
        //             ];
        //     $postData = [
        //         'properties' => [
        //                             'nama' => [
        //                                 'type' => 'text',
        //                                 "fielddata" => true
        //                             ]
        //                         ]
        //     ];
        //     curl_setopt($ch, CURLOPT_URL,config('app.ES_url').'/project/_mapping');
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        //     $result     = curl_exec ($ch);
        //     $hasil = json_decode($result);
        // }catch (\Throwable $th) {
        // }
    }

    // sudah
    public function getUser(){
        try {
            $get = User::get();
            $data['data']    = $get;
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Gagal, Mohon Reload Page';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    // sudah
    public function uploadphoto($kategori){
        try {
            $validator = Validator::make(request()->all(), [
                'filename'      => "required",
                'path'          => "required",
                'filesize'      => 'required',
                'extension'     => 'required',
            ]);

            // handle jika tidak terpenuhi
            if ($validator->fails()) {
                $data_error['message'] = $validator->errors();
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            if (request()->del) {
                $temp = TempUpload::where('path', request()->del)->first();
                if ($temp) {
                    $temp->delete();
                }
            }

            TempUpload::create([
                'jenis'     => $kategori,
                'nama_file' => request()->filename,
                'path'      => request()->path,
                'size'      => request()->filesize,
                'type'      => request()->extension
            ]);

            return response()->json([
                "status"    => 1,
                "data"      => request()->path,
            ],200);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    // sudah
    public function delete($kategori){
        $path = request()->path;

        $cek = TempUpload::where('path',$path)->first();
        if ($cek) {
            $cek->delete();
        }

        return request()->all();
    }

    public function deleteOnLeave(){
        $path = request()->path;

        for ($i=0; $i < count($path) ; $i++) {
            $cek = TempUpload::where('path', $path[$i])->first();
            if ($cek) {
                $cek->delete();
            }
        }

        return request()->all();
    }

    // sudah
    public function upimgcontent(){
        if (request()->hasFile('upload')) {
            $originName = request()->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = request()->file('upload')->getClientOriginalExtension();
            $fileName = $fileName."_".time().".".$extension;

            request()->file('upload')->move(public_path('storage/content'), $fileName);

            $CKEditorFuncNum = request()->input('CKEditorFuncNum');
            $url = asset('storage/content/'.$fileName);
            $msg = 'Image Uploaded Successfully';

            return response()->json([
                'uploaded' => '1',
                'fileName' => $fileName,
                'url' => $url
            ]);
        }

        // NOTES
        // https://www.narindersingh.in/incorrect-server-response-on-image-drag-and-drop-in-ckeditor-with-laravel-application/
        // //get filename with extension
        // $fileNameWithExtension = $request->file('upload')->getClientOriginalName();

        // //get filename without extension
        // $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

        // //get file extension
        // $extension = $request->file('upload')->getClientOriginalExtension();

        // //filename to store
        // $fileNameToStore = $fileName.'_'.time().'.'.$extension;

        // //Upload File
        // $request->file('upload')->storeAs('uploads', $fileNameToStore);

        // $CKE
        // if($CKEditorFuncNum > 0){
                // ini jika case menggunakan CKEDITOR 4 method FORM
        //     $url = asset('storage/uploads/'.$fileNameToStore);
        //     $msg = 'Image successfully uploaded';
        //     $renderHtml = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

        //     // Render HTML output
        //     @header('Content-type: text/html; charset=utf-8');
        //     echo $renderHtml;

        // } else {
                // ini jika case menggunakan CKEDITOR 4 method XHR
        //     $url = asset('storage/uploads/'.$fileNameToStore);
        //     $msg = 'Image successfully uploaded';
        //     $renderHtml = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
        //     return response()->json([
        //         'uploaded' => '1',
        //         'fileName' => $fileNameToStore,
        //         'url' => $url
        //     ]);
        // }
    }

    // sudah
    public function create(){
        $validator = Validator::make(request()->all(), [
            'photo'         => "required",
            'direktorat'    => "required",
            'divisi'        => 'required',
            'nama_project'  => 'required',
            'status'        => 'required',
            'tgl_mulai'     => 'required',
            'pm'            => 'required',
            'emailpm'       => 'required',
            'jenispekerja'  => 'required',
            'konsultant'    => 'required',
            'restricted'    => 'required',
            'deskripsi'     => 'required',
            'metodologi'    => 'required',
            'keyword'       => 'required',
            'restricted'    => 'required',
            'user'          => 'required',
            'attach'        => 'required',
            'checker'       => 'required',
            'signer'        => 'required',
            'token_bri'     => "required",
        ]);

        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message'] = $validator->errors();
            $data_error['error_code'] = 1; //error
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 400);
        }

        try {
            // --------------------------
            // cek akun checker
            $cekChecker =   User::where('personal_number',(int)request()->checker)->first();
            if (isset($cekChecker->personal_number)) {
                $dataChecker = $cekChecker;
            }else{
                $cheking = $this->getDataUser(request()->token_bri,(int)request()->checker);
                if($cheking == false){
                    $data['message']    =   'Get Data Checker Failed';
                    $data['error_code'] = 0; //error
                    return response()->json([
                        'status'    =>  0,
                        'data'      =>  $data
                    ],200);
                }else{
                    $dataChecker    =   $cheking;
                }
            }

            // cek akun signer
            $cekSigner =   User::where('personal_number',(int)request()->signer)->first();
            if (isset($cekSigner->personal_number)) {
                $dataSigner = $cekSigner;
            }else{
                $cheking = $this->getDataUser(request()->token_bri,(int)request()->signer);
                if($cheking == false){
                    $data['message']    =   'Get Data Signer Failed';
                    $data['error_code'] = 0; //error
                    return response()->json([
                        'status'    =>  0,
                        'data'      =>  $data
                    ],200);
                }else{
                    $dataSigner    =   $cheking;
                }
            }
            // --------------------------

            // date
            date_default_timezone_set('Asia/Jakarta');
            $now = Carbon::now();
            $waktu = $now->year."".$now->month."".$now->day."".$now->hour."".$now->minute."".$now->second;

            // project manager
            $create_pm   = Project_managers::create([
                'nama'      =>  request()->pm,
                'email'     =>  request()->emailpm,
            ]);

            // restricted
            if (request()->restricted == null || request()->restricted == "0") {
                $restricted = '0';
            }else{
                $restricted = '1';
            }

            // create_project
            $create_project = Project::create([
                'divisi_id'             => request()->divisi,
                'project_managers_id'   => $create_pm->id,
                'nama'                  => request()->nama_project,
                'slug'                  => $waktu."-".\Str::slug(request()->nama_project),
                'thumbnail'             => request()->photo,
                'deskripsi'             => request()->deskripsi,
                'metodologi'            => request()->metodologi,
                'tanggal_mulai'         => request()->tgl_mulai,
                'tanggal_selesai'       => request()->tgl_selesai,
                'status_finish'         => request()->status,
                'is_recomended'         => 0,
                'is_restricted'         => $restricted,
                'flag_mcs'              => request()->mode,
                'user_maker'            => Auth::User()->personal_number,
                'user_checker'          => $dataChecker->personal_number,
                'user_signer'           => $dataSigner->personal_number,
            ]);

            // vendor
            $tampung_vendor     =   request()->konsultant;
            if ($tampung_vendor !== 'internal') {
                for ($i=0; $i < count($tampung_vendor) ; $i++) {
                    $cek_konsultant = Consultant::find($tampung_vendor[$i]);

                    if (isset($cek_konsultant->id)) {
                        $simpanvendor['project_id']       =   $create_project->id;
                        $simpanvendor['consultant_id']    =   $tampung_vendor[$i];
                        ConsultantProject::create($simpanvendor);
                    }else{
                        $cons_new['nama']                  =   $tampung_vendor[$i];
                        $cons_new['tentang']               =   '-';
                        $cons_new['bidang']                =   '-';
                        $cons_new['website']               =   '-';
                        $cons_new['telepon']               =   '-';
                        $cons_new['email']                 =   'email@mail.com';
                        $cons_new['facebook']              =   '-';
                        $cons_new['instagram']             =   '-';
                        $cons_new['lokasi']                =   '-';
                        $create_cons                       =   Consultant::create($cons_new);

                        $simpanvendor['project_id']       =   $create_project->id;
                        $simpanvendor['consultant_id']    =   $create_cons->id;
                        ConsultantProject::create($simpanvendor);
                    }
                }
            }

            // lesson learned
            $tampung_lesson                 =   request()->lesson;
            $tampung_lesson_keterangan      =   request()->lesson_keterangan;
            $tampung_tahap                  =   request()->tahap;
            if (isset($tampung_lesson)) {
                for ($i=0; $i < count($tampung_lesson) ; $i++) {
                    if($tampung_lesson[$i] <> '' or $tampung_lesson_keterangan[$i] <> ''){
                        $simpanlesson['project_id']         =   $create_project->id;
                        $simpanlesson['divisi_id']          =   request()->divisi;
                        $simpanlesson['lesson_learned']     =   $tampung_lesson[$i];
                        $simpanlesson['tahap']              =   $tampung_tahap[$i];
                        $simpanlesson['detail']             =   $tampung_lesson_keterangan[$i];
                        Lesson_learned::create($simpanlesson);
                    }
                }
            }

            // keyword
            $tampung_keyword     =   request()->keyword;
            if (isset($tampung_keyword)) {
                for ($i=0; $i < count($tampung_keyword) ; $i++) {
                    $cek = Keywords::where('nama',$tampung_keyword[$i])->first();
                    if ($cek) {
                        $simpankeyword['project_id']       =   $create_project->id;
                        $simpankeyword['nama']             =   $cek->nama;
                    }else{
                        $simpankeyword['project_id']       =   $create_project->id;
                        $simpankeyword['nama']             =   $tampung_keyword[$i];
                    }
                    Keywords::create($simpankeyword);
                }
            }

            // attach
            $tampung_attach     =   request()->attach;
            if (isset($tampung_attach)) {
                for ($i=0; $i < count($tampung_attach) ; $i++) {
                    $cek = TempUpload::where('path',$tampung_attach[$i])->first();
                    if($cek){
                            $simpanattach['project_id']   =   $create_project->id;
                            $simpanattach['nama']         =   $cek->nama_file;
                            $simpanattach['jenis_file']   =   $cek->type;
                            $simpanattach['url_file']     =   $cek->path;
                            $simpanattach['size']         =   $cek->size;
                            Document::create($simpanattach);

                            $cek->delete();
                    }
                }
            }

            // restricted ?
            if (request()->restricted == 1) {
                $user     =   request()->user;
                for ($i=0; $i < count($user) ; $i++) {
                    $simpanuser['project_id']   =   $create_project->id;
                    $simpanuser['user_id']      =   (int)$user[$i];
                    Restriction::create($simpanuser);
                }
            }

            // Notifikasi
            // jika mode createnya ialah pengajuan dan bukan save to draft maka buatkan notifnya
            if (request()->mode == 1) {
                // buat maker
                $Notifikasi   = notifikasi::create([
                    'user_id'      =>  Auth::user()->personal_number,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Sedang Dicheck',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$create_project->nama."</b> Akan Di <b class='text-info'>Check</b> Oleh <b>".$create_project->userchecker->name."</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);

                // buat checker
                $Notifikasi   = notifikasi::create([
                    'user_id'      =>  $create_project->user_checker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Baru Diminta Melakukan Pengecheckan',
                    'pesan'        =>  "Terdapat data proyek <b>".$create_project->nama."</b> yang membutuhkan Approval Anda",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
            }

            // Event Activity
            event(new TriggerActivityEvent(1, Auth::user()->personal_number));

            // refresh ES
            //$this->reloadES();

            $data['message']    =   'Save Data Berhasil';
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Create Data Gagal, Mohon Coba Lagi';
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function prepare_form()
    {
        try {
            $data   = [];

            $querydirektorat  = Divisi::select('direktorat')->groupBy('direktorat')->get();
            $query            = Divisi::get();
            $tags             = keywords::distinct()->get(['nama']);
            $consultant       = Consultant::get();
            // $checker= User::where('divisi',Auth::User()->divisi)->where('role','1')->get();
            // $signer = User::where('divisi',Auth::User()->divisi)->where('role','2')->get();

            $data['direktorat'] = $querydirektorat;
            $data['divisi'] = $query;
            $data['tags'] = $tags;
            $data['consultant'] = $consultant;
            // $data['checker'] = $checker;
            // $data['signer'] = $signer;

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

    //sudah
    public function prepare_form_edit($slug)
    {
        try {
            $data   = [];

            // if (Auth::User()->role == 0) {
            //     $project        = Project::with(['user_restrict','document'])->where('slug',$slug)->where('user_maker', Auth::user()->personal_number)->first();
            // }elseif (Auth::User()->role == 1) {
            //     $project        = Project::with(['user_restrict','document'])->where('slug',$slug)->where('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)->first();
            // }elseif (Auth::User()->role == 2) {
            //     $project        = Project::with(['user_restrict','document'])->where('slug',$slug)->where('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)->first();
            // }elseif (Auth::User()->role == 3) {
            //     $temp = [3,4,5,6];
            //     $project        = Project::with(['user_restrict','document'])->where('slug',$slug)->whereIn('flag_mcs', $temp)->first();
            // }

            // cek project
            if (Auth::User()->role == 3) {
                $temp       = [3,4,5,6];
                $project    = Project::with(['consultant','divisi','keywords', 'lesson_learned','project_managers', 'document', 'favorite_project','user_restrict'])->where('slug',$slug)->whereIn('flag_mcs', $temp)->first();
            }else{
                $project        = Project::with(['consultant','divisi','keywords', 'lesson_learned','project_managers', 'document', 'favorite_project','user_restrict'])->where('slug',$slug)->where(function($q){
                    $q->orWhere('user_maker', Auth::User()->personal_number);
                    $q->orWhere('user_checker', Auth::User()->personal_number)->where('flag_mcs', 1);
                    $q->orWhere('user_signer', Auth::User()->personal_number)->where('flag_mcs', 2);
                })->first();
            }

            if (!$project) {
                $data_error['message'] = 'Proyek tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }


            $query          = Divisi::get();
            $consultant     = Consultant::get();
            // $user           = User::get();
            $tags           = keywords::distinct()->get(['nama']);
            // $checker        = User::where('divisi',Auth::User()->divisi)->where('role','1')->get();
            // $signer         = User::where('divisi',Auth::User()->divisi)->where('role','2')->get();

            $data['data']       = $project;
            $data['consultant'] = $consultant;
            $data['direktorat'] = $query;
            $data['tags']       = $tags;
            // $data['checker']    = $checker;
            // $data['signer']     = $signer;
            // $data['user']       = $user;

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

    //sudah
    public function update($id){
        $validator = Validator::make(request()->all(), [
            'photo'         => "required",
            'direktorat'    => "required",
            'divisi'        => 'required',
            'nama_project'  => 'required',
            'status'        => 'required',
            'tgl_mulai'     => 'required',
            'pm'            => 'required',
            'emailpm'       => 'required',
            'jenispekerja'  => 'required',
            'konsultant'    => 'required',
            'restricted'    => 'required',
            'deskripsi'     => 'required',
            'metodologi'    => 'required',
            'keyword'       => 'required',
            'restricted'    => 'required',
            'user'          => 'required',
            'attach'        => 'required',
            'checker'       => 'required',
            'signer'        => 'required',
            'token_bri'     => "required",
        ]);

        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message'] = $validator->errors();
            $data_error['error_code'] = 1; //error
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 400);
        }

        try {
            // --------------------------
            // cek akun checker
            $cekChecker =   User::where('personal_number',(int)request()->checker)->first();
            if (isset($cekChecker->personal_number)) {
                $dataChecker = $cekChecker;
            }else{
                $cheking = $this->getDataUser(request()->token_bri,(int)request()->checker);
                if($cheking == false){
                    $data['message']    =   'Get Data Checker Failed';
                    $data['error_code'] = 0; //error
                    return response()->json([
                        'status'    =>  0,
                        'data'      =>  $data
                    ],200);
                }else{
                    $dataChecker    =   $cheking;
                }
            }

            // cek akun signer
            $cekSigner =   User::where('personal_number',(int)request()->signer)->first();
            if (isset($cekSigner->personal_number)) {
                $dataSigner = $cekSigner;
            }else{
                $cheking = $this->getDataUser(request()->token_bri,(int)request()->signer);
                if($cheking == false){
                    $data['message']    =   'Get Data Signer Failed';
                    $data['error_code'] = 0; //error
                    return response()->json([
                        'status'    =>  0,
                        'data'      =>  $data
                    ],200);
                }else{
                    $dataSigner    =   $cheking;
                }
            }
            // --------------------------


            // cek project
            $cek    =   Project::where('id',$id)->first();
            if (!$cek) {
                $data_error['message'] = 'Project Not Found';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            // date
            date_default_timezone_set('Asia/Jakarta');
            $now = Carbon::now();
            $waktu = $now->year."".$now->month."".$now->day."".$now->hour."".$now->minute."".$now->second;

            // project manager
            $cek_pm    =   Project_managers::where('nama',request()->pm)->where('email',request()->emailpm)->first();
            if (!$cek_pm) {
                $cek_pm   = Project_managers::create([
                    'nama'      =>  request()->pm,
                    'email'     =>  request()->emailpm,
                ]);
            }

            // restricted
            if (request()->restricted == null || request()->restricted == "0") {
                $restricted = '0';
            }else{
                $restricted = '1';
            }

            //default catatan checker/signer kosong
            $r_note1 = "";
            $r_note2 = "";

            // role
            // cek role
            if (Auth::user()->role <> 3) {
                $role = 0;
                if ($cek->user_maker == Auth::user()->personal_number && ($cek->flag_mcs == 0)) {
                    $role = 0;
                }elseif($cek->user_checker == Auth::user()->personal_number && ($cek->flag_mcs == 1)){
                    $role = 1;
                }elseif($cek->user_signer == Auth::user()->personal_number && ($cek->flag_mcs == 2)){
                    $role = 2;
                }
            }else{
                $role = 3;
            }

            // flag_mcs
            $flag_mcs_yang_lama = $cek->flag_mcs;
            date_default_timezone_set('Asia/Jakarta');
            if ($role == 0) {
                $flag_mcs = request()->mode;
                $r_note1 = "";
                $r_note2 = "";
            }elseif($role == 1){
                $flag_mcs = 1;
                if (request()->mode == 1) {
                    $flag_mcs = 2;
                    // sisipkan data ke db
                    $data_baru['checker_at']  = Carbon::now();
                }
            }elseif($role == 2){
                $flag_mcs = 2;
                if (request()->mode == 1) {
                    $flag_mcs = 3;
                    // sisipkan data ke db
                    $data_baru['signer_at']  = Carbon::now();
                }
            }elseif($role == 3){
                if (request()->mode == 1) {
                    if ($cek->flag_mcs == 3) {
                        $data_baru['review_at']   = Carbon::now();
                        $data_baru['flag_es']     = NULL;
                    }
                    $flag_mcs = 5;
                    // sisipkan data ke db
                    $data_baru['publish_at']  = Carbon::now();
                }else{
                    if ($cek->flag_mcs == 5) {
                        $data_baru['flag_es']     = NULL;
                    }
                    $flag_mcs = 4;
                    // sisipkan data ke db
                    $data_baru['review_at']  = Carbon::now();
                }
            }

            $data_baru['divisi_id']           = request()->divisi;
            $data_baru['project_managers_id'] = $cek_pm->id;
            $data_baru['nama']                = request()->nama_project;
            $data_baru['slug']                = $waktu."".\Str::slug(request()->nama_project);
            $data_baru['thumbnail']           = request()->photo;
            $data_baru['deskripsi']           = request()->deskripsi;
            $data_baru['metodologi']          = request()->metodologi;
            $data_baru['tanggal_mulai']       = request()->tgl_mulai;
            $data_baru['tanggal_selesai']     = request()->tgl_selesai;
            $data_baru['status_finish']       = request()->status;
            $data_baru['is_restricted']       = $restricted;
            $data_baru['flag_mcs']            = $flag_mcs;
            $data_baru['r_note1']             = $r_note1;
            $data_baru['r_note2']             = $r_note2;
            // $data_baru['user_maker']       = Auth::User()->personal_number;
            $data_baru['user_checker']        = $dataChecker->personal_number;
            $data_baru['user_signer']         = $dataSigner->personal_number;

            // create_project
            $cek->update($data_baru);

            // vendor
            $tampung_vendor     =   request()->konsultant;
            if ($tampung_vendor !== 'internal') {
                ConsultantProject::where('project_id',$cek->id)->delete();
                for ($i=0; $i < count($tampung_vendor) ; $i++) {
                    // project manager
                    $cek_konsultant = Consultant::find((int)$tampung_vendor[$i]);

                    if (isset($cek_konsultant->id)) {
                        $simpanvendor['project_id']       =   $cek->id;
                        $simpanvendor['consultant_id']    =   $tampung_vendor[$i];
                        ConsultantProject::create($simpanvendor);
                    }else{
                        $cons_new['nama']                  =   $tampung_vendor[$i];
                        $cons_new['tentang']               =   '-';
                        $cons_new['bidang']                =   '-';
                        $cons_new['website']               =   '-';
                        $cons_new['telepon']               =   '-';
                        $cons_new['email']                 =   'email@mail.com';
                        $cons_new['facebook']              =   '-';
                        $cons_new['instagram']             =   '-';
                        $cons_new['lokasi']                =   '-';
                        $create_cons                       =   Consultant::create($cons_new);

                        $simpanvendor['project_id']       =   $cek->id;
                        $simpanvendor['consultant_id']    =   $create_cons->id;
                        ConsultantProject::create($simpanvendor);
                    }
                }
            }

            // lesson learned
            $tampung_lesson                 =   request()->lesson;
            $tampung_lesson_keterangan      =   request()->lesson_keterangan;
            $tampung_tahap                  =   request()->tahap;
            if (isset($tampung_lesson)) {
                Lesson_learned::where('project_id',$cek->id)->delete();

                for ($i=0; $i < count($tampung_lesson) ; $i++) {
                    if($tampung_lesson[$i] <> '' or $tampung_lesson_keterangan[$i] <> ''){
                        $simpanlesson['project_id']         =   $cek->id;
                        $simpanlesson['divisi_id']          = request()->divisi;
                        $simpanlesson['tahap']               =   $tampung_tahap[$i];
                        $simpanlesson['lesson_learned']     =   $tampung_lesson[$i];
                        $simpanlesson['detail']             =   $tampung_lesson_keterangan[$i];
                        Lesson_learned::create($simpanlesson);
                    }
                }
            }

            // keyword
            $tampung_keyword     =   request()->keyword;
            if (isset($tampung_keyword)) {
                Keywords::where('project_id',$cek->id)->delete();
                for ($i=0; $i < count($tampung_keyword) ; $i++) {
                    $cek_keyword = Keywords::where('nama',$tampung_keyword[$i])->first();
                    if ($cek_keyword) {
                        $simpankeyword['project_id']       =   $cek->id;
                        $simpankeyword['nama']             =   $cek_keyword->nama;
                    }else{
                        $simpankeyword['project_id']       =   $cek->id;
                        $simpankeyword['nama']             =   $tampung_keyword[$i];
                    }
                    Keywords::create($simpankeyword);
                }
            }

            // attach
            // screening
            $tampung_attach     =   request()->attach;
            if (isset($tampung_attach)) {
                document::whereNotIn('nama',$tampung_attach)->where('project_id',$id)->delete();
            }

            $tampung_attach     =   request()->attach;
            if (isset($tampung_attach)) {
                for ($i=0; $i < count($tampung_attach) ; $i++) {
                    $cek_temp = TempUpload::where('path',$tampung_attach[$i])->first();
                    if($cek_temp){
                        $simpanattach['project_id']   =   $cek->id;
                        $simpanattach['nama']         =   $cek_temp->nama_file;
                        $simpanattach['jenis_file']   =   $cek_temp->type;
                        $simpanattach['url_file']     =   $cek_temp->path;
                        $simpanattach['size']         =   $cek_temp->size;
                        Document::create($simpanattach);

                        $cek_temp->delete();
                    }
                }
            }

            // restricted ?
            if (request()->restricted == 1) {
                $user     =   request()->user;
                Restriction::where('project_id',$cek->id)->delete();
                for ($i=0; $i < count($user) ; $i++) {
                    $cek_user = user::where('personal_number',$user[$i])->first();
                    if($cek_user){
                            $simpanuser['project_id']   =   $cek->id;
                            $simpanuser['user_id']      =   $cek_user->personal_number;
                            Restriction::create($simpanuser);
                    }
                }
            }

            // Notifikasi
            // jika mode createnya ialah pengajuan dan bukan save to draft maka buatkan notifnya
            if (($flag_mcs == 1 && $flag_mcs > $flag_mcs_yang_lama) || ($flag_mcs == 1 && $flag_mcs_yang_lama == 7)) {
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  Auth::user()->personal_number,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Sedang Dicheck',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Akan Di <b class='text-info'>Check</b> Oleh <b>".$cek->userchecker->name."</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
                // buat checker
                $Notifikasi   = notifikasi::create([
                    'user_id'      =>  $cek->user_checker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Baru Diminta Melakukan Pengecheckan',
                    'pesan'        =>  "Terdapat data proyek <b>".$cek->nama."</b> yang membutuhkan Approval Anda",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
            }elseif($flag_mcs == 2 && $flag_mcs > $flag_mcs_yang_lama){
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  $cek->user_maker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Sedang Disigner',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Akan Di <b class='text-info'>Signer</b> Oleh <b>".$cek->usersigner->name."</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
                // buat signer
                $Notifikasi   = notifikasi::create([
                    'user_id'      =>  $cek->user_signer,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Baru Diminta Melakukan Penandatangan',
                    'pesan'        =>  "Terdapat data proyek <b>".$cek->nama."</b> yang membutuhkan Approval Anda",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
            }elseif($flag_mcs == 3 && $flag_mcs > $flag_mcs_yang_lama){
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  $cek->user_maker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Sedang Direview',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Akan Di <b class='text-info'>Review</b> Oleh <b>Admin</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
            }elseif($flag_mcs == 5 && $flag_mcs > $flag_mcs_yang_lama){
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  $cek->user_maker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Telah Terpublish',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Telah Di <b class='text-info'>Publish</b> Oleh <b>Admin</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);

                // Event Activity
                event(new TriggerActivityEvent(2, $cek->user_maker));
            }

            // refresh ES
            // $this->reloadES();

            $data['message']    =   'Save Data Berhasil';
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Get Data Gagal, Mohon Reload Page';
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    // sudah
    public function approval(){
        try {
            $id =   request()->id;

            // // cek project
            // if (Auth::User()->role == 0) {
            //     $data_error['message'] = 'Proyek tidak dapat diakses!';
            //     $data_error['error_code'] = 1; //error
            //     return response()->json([
            //         'status' => 0,
            //         'data'  => $data_error
            //     ], 400);
            // }elseif (Auth::User()->role == 1) {
            //     $cek    =   Project::where('id',$id)->where('user_checker',Auth::User()->personal_number)->first();
            // }elseif (Auth::User()->role == 2) {
            //     $cek    =   Project::where('id',$id)->where('user_signer',Auth::User()->personal_number)->first();
            // }elseif (Auth::User()->role == 3) {
            //     $cek    =   Project::where('id',$id)->first();
            // }

            // cek project
            if (Auth::User()->role == 3) {
                $cek    =   Project::where('id',$id)->first();
            }else{
                $cek    =   Project::where('id',$id)->where(function($q){
                    $q->orWhere('user_checker', Auth::User()->personal_number);
                    $q->orWhere('user_signer', Auth::User()->personal_number);
                })->first();
            }

            if (!$cek) {
                $data_error['message'] = 'Proyek tidak ditemukan!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }

            // cek role
            if (Auth::user()->role <> 3) {
                $role = 0;
                if ($cek->user_maker == Auth::user()->personal_number && ($cek->flag_mcs == 0)) {
                    $role = 0;
                }elseif($cek->user_checker == Auth::user()->personal_number && ($cek->flag_mcs == 1)){
                    $role = 1;
                }elseif($cek->user_signer == Auth::user()->personal_number && ($cek->flag_mcs == 2)){
                    $role = 2;
                }
            }else{
                $role = 3;
            }

            // flag_mcs
            $flag_mcs_yang_lama = $cek->flag_mcs;
            date_default_timezone_set('Asia/Jakarta');
            if ($role == 0) {
                $data_error['message'] = 'Proyek tidak dapat diakses!';
                $data_error['error_code'] = 1; //error
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 400);
            }elseif($role == 1){
                $flag_mcs = 2;
                // sisipkan data ke db
                $data_baru['checker_at']  = Carbon::now();
            }elseif($role == 2){
                $flag_mcs = 3;
                // sisipkan data ke db
                $data_baru['signer_at']  = Carbon::now();
            }elseif($role == 3){
                if ($cek->flag_mcs == 3) {
                    $data_baru['review_at']   = Carbon::now();
                    $data_baru['flag_es']     = NULL;
                }
                $data_baru['publish_at']  = Carbon::now();
                $flag_mcs = 5;
                // sisipkan data ke db
            }

            // update_project
            $data_baru['flag_mcs']            = $flag_mcs;
            $cek->update($data_baru);

            // Notifikasi
            // jika mode createnya ialah pengajuan dan bukan save to draft maka buatkan notifnya
            if($flag_mcs == 2 && $flag_mcs > $flag_mcs_yang_lama){
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  $cek->user_maker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Sedang Disigner',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Akan Di <b class='text-info'>Signer</b> Oleh <b>".$cek->usersigner->name."</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
                // buat signer
                $Notifikasi   = notifikasi::create([
                    'user_id'      =>  $cek->user_signer,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Baru Diminta Melakukan Penandatangan',
                    'pesan'        =>  "Terdapat data proyek <b>".$cek->nama."</b> yang membutuhkan Approval Anda",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
            }elseif($flag_mcs == 3 && $flag_mcs > $flag_mcs_yang_lama){
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  $cek->user_maker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Sedang Direview',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Akan Di <b class='text-info'>Review</b> Oleh <b>Admin</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);
            }elseif($flag_mcs == 5 && $flag_mcs > $flag_mcs_yang_lama){
                $Notifikasi         = notifikasi::create([
                    'user_id'      =>  $cek->user_maker,
                    'kategori'     =>  1,
                    'judul'        =>  'Proyek Telah Terpublish',
                    'pesan'        =>  "Proyek Anda Dengan Nama <b>".$cek->nama."</b> Telah Di <b class='text-info'>Publish</b> Oleh <b>Admin</b>",
                    'direct'       =>  config('app.FE_url')."myproject",
                    'status'       =>  0,
                ]);

                // Event Activity
                event(new TriggerActivityEvent('2', $cek->user_maker));
            }

            // refresh ES
            // if(Auth::User()->role == 3){
            //     $this->reloadES();
            // }

            $data['message']    =   'Proyek berhasil disetujui.';
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Proses Data Gagal, Mohon Reload Page';
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    // sudah
    public function hapus(){
        try {
            $id = request()->id;
            $q  = Project::find($id);
            $q->delete();
            // refresh ES
            // $this->reloadES();

            $data['message']    =   'Proyek berhasil dihapus.';
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Proses Data Gagal, Mohon Reload Page';
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    // sudah
    public function send(){
        try {
            $id =   request()->id;
            $q = Project::find($id);
            $q->flag_mcs = 1;
            $q->save();

            // buat maker
            $Notifikasi   = notifikasi::create([
                'user_id'      =>  Auth::user()->personal_number,
                'kategori'     =>  1,
                'judul'        =>  'Proyek Sedang Dicheck',
                'pesan'        =>  "Proyek Anda Dengan Nama <b>".$q->nama."</b> Akan Di <b class='text-info'>Check</b> Oleh <b>".$q->userchecker->name."</b>",
                'direct'       =>  config('app.FE_url')."myproject",
                'status'       =>  0,
            ]);

            // buat checker
            $Notifikasi   = notifikasi::create([
                'user_id'      =>  $q->user_checker,
                'kategori'     =>  1,
                'judul'        =>  'Proyek Baru Diminta Melakukan Pengecheckan',
                'pesan'        =>  "Terdapat data proyek <b>".$q->nama."</b> yang membutuhkan Approval Anda",
                'direct'       =>  config('app.FE_url')."myproject",
                'status'       =>  0,
            ]);

            // refresh ES
            // $this->reloadES();

            $data['message']    =   'Proyek berhasil dikirim.';
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Proses Data Gagal, Mohon Reload Page';
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function reject(){
        $id =   request()->id;
        $q = Project::find($id);

        // role
        // cek role
        if (Auth::user()->role <> 3) {
            $role = 0;
            if ($q->user_maker == Auth::user()->personal_number && ($q->flag_mcs == 0)) {
                $role = 0;
            }elseif($q->user_checker == Auth::user()->personal_number && ($q->flag_mcs == 1)){
                $role = 1;
            }elseif($q->user_signer == Auth::user()->personal_number && ($q->flag_mcs == 2)){
                $role = 2;
            }
        }else{
            $role = 3;
        }

        if ($role == 1) { //CHECKER
            $q->flag_mcs = 7;
            $q->r_note1 = request()->note; //CATATAN MENGAPA DIREJECT CHECKER

            // reject dari checker
            $Notifikasi   = notifikasi::create([
                'user_id'      =>  $q->user_maker,
                'kategori'     =>  1,
                'judul'        =>  'Proyek Direject',
                'pesan'        =>  "Proyek Anda Dengan Nama <b>".$q->nama."</b> Telah Direject <b class='text-danger'>Reject</b> Oleh <b>".$q->userchecker->name."</b>",
                'direct'       =>  config('app.FE_url')."myproject",
                'status'       =>  0,
            ]);
        } elseif ($role == 2) { //SIGNER
            $q->flag_mcs = 7;
            $q->r_note2 = request()->note; //CATATAN MENGAPA DIREJECT SIGNER

            // reject dari checker
            $Notifikasi   = notifikasi::create([
                'user_id'      =>  $q->user_maker,
                'kategori'     =>  1,
                'judul'        =>  'Proyek Direject',
                'pesan'        =>  "Proyek Anda Dengan Nama <b>".$q->nama."</b> Telah Direject <b class='text-danger'>Reject</b> Oleh <b>".$q->usersigner->name."</b>",
                'direct'       =>  config('app.FE_url')."myproject",
                'status'       =>  0,
            ]);
        }

        try {
            $q->save();
            // refresh ES
            // $this->reloadES();

            $data['message']    =   'Proyek berhasil ditolak.';
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Proses Data Gagal, Mohon Reload Page';
            $data['error_code'] = 0; //error
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function getDataUser($token_bri, $pn){
        try{
            $access_token   =   $token_bri;
            $ch2 = curl_init();
            $headers  = [
                        'Content-Type: application/json',
                        'Accept: application/json',
                        "Authorization: Bearer $access_token",
                    ];
            $postData = [
                'pernr'     => $pn,
            ];
            curl_setopt($ch2, CURLOPT_URL,config('app.api_detail_pekerja_bristar'));
            curl_setopt($ch2, CURLOPT_POST, 1);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
            $result2     = curl_exec ($ch2);
            $hasil2      = json_decode($result2);
            if (isset($hasil2->responseData->COST_CENTER)) {
                // save data
                $ResponseCostcenter = $hasil2->responseData->COST_CENTER;
                $Responsenama       = $hasil2->responseData->NAMA;

                // cek
                if ($ResponseCostcenter <> null) {
                    $dataDivisi = Divisi::where('cost_center', $ResponseCostcenter)->first();
                    // add user
                    $new                    = new User();
                    $new->name              = $Responsenama??'User';
                    $new->personal_number   = $pn;
                    $new->username          = $Responsenama??'User';
                    $new->email             = str_replace(" ",".",$Responsenama)."@gmail.com";
                    $new->role              = 3;
                    $new->divisi            = isset($dataDivisi->id)? $dataDivisi->id : 11111111;
                    $new->last_login_at     = Carbon::now();
                    $new->xp                = 0;
                    $new->avatar_id         = 1;
                    $new->save();

                    // create Admin success
                    return $new;
                }
            }
        }catch(\Throwable $th){
            return false;
        }
    }
}
