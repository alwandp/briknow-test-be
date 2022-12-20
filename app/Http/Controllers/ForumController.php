<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\TriggerActivityEvent;
use App\Forum;
use App\ForumUser;
use App\User;
use App\ForumComment;
use App\Level;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Embed\Embed;
use stdClass;

class ForumController extends Controller
{
    public function index()
    {
        // $query      = Forum::with(['user', 'forumcomment'])->where('status', '1')->get();
        $myQuery    = Forum::with('user')->where('user_id', Auth::user()->personal_number)->where('status', '1')->orderby('updated_at','desc')->get();
        $draft      = Forum::where('status', '0')->where('user_id', Auth::user()->personal_number)->orderby('updated_at','desc')->get();
        
        // return $myQuery;

        try {          
            $data['message']        =   'GET Berhasil.';
            $data['myData']         =   $myQuery;
            $data['draft']          =   $draft;
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

    public function prepare_form(){
        try {
            $user  = User::get();

            $data['user'] = $user;
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
    
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'kategori'      => "required",
            'judul'         => "required",
            'hakakses'      => "required",
            'content'       => "required",
            'status'      => "required",
        ]);
        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message']      = $validator->errors();
            $data_error['error_code']   = 1; //error
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 200);            
        }

        try {
            // cek kategori
            if (request()->kategori == 'post'){
                $kategori = 0;
            } elseif (request()->kategori == 'url'){
                $kategori = 1;
            }

            // status hak akses
            if (request()->hakakses == 1) {
                // cek validasi list user
                $validator = Validator::make(request()->all(), [
                    'listuser'      => "required",
                ]);

                // handle jika tidak terpenuhi
                if ($validator->fails()) {
                    $data_error['message']      = $validator->errors();
                    $data_error['error_code']   = 1; //error
                    return response()->json([
                        'status' => 0,
                        'data'  => $data_error
                    ], 200);            
                }
                
                $listuser   = request()->listuser;
            }

            // date
            date_default_timezone_set('Asia/Jakarta');
            $now = Carbon::now();
            $waktu = $now->year."".$now->month."".$now->day."".$now->hour."".$now->minute."".$now->second;

            // create
            $add['user_id']             =   Auth::user()->personal_number;
            $add['slug']                =   $waktu;
            $add['judul']               =   (string)request()->judul;
            $add['content']             =   (string)request()->content;
            $add['kategori']            =   $kategori;
            $add['status']              =   request()->status;
            $add['restriction']         =   request()->hakakses;
            $createforum                =   Forum::create($add);

            // restricted ?
            if (request()->hakakses == 1) {
                for ($i=0; $i < count($listuser) ; $i++) { 
                    $simpanuser['forum_id']     =   $createforum->id;
                    $simpanuser['user_id']      =   (int)$listuser[$i];
                    ForumUser::create($simpanuser);
                }
            }

            // Event Activity
            event(new TriggerActivityEvent(3, Auth::user()->personal_number));

            $data['message']        =   'Create Post Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);

        } catch (\Throwable $th) {
            $datas['message']    =   'Create Post failed';
            $datas['errornya']    =   $th;
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function draft($id)
    {
        // get data forum
        $query = Forum::with('user')->find($id);

        if (!isset($query->judul)) {
            $datas['message']    =   'Akses Ditolak';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }elseif ($query->user_id <> Auth::user()->personal_number && Auth::user()->role <> 3) {
            $datas['message']    =   'Akses Ditolak';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }

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

    public function saveDraft()
    {
        $validator = Validator::make(request()->all(), [
            'judul'         => "required",
            'content'       => "required",
            'kategori'      => "required",
            'hakakses'      => "required",
            'status'        => "required",
        ]);
        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message']      = $validator->errors();
            $data_error['error_code']   = 1; //error
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 200);            
        }

        if (request()->status == 1) {
            $pesan = 'Publish';
        } else {
            $pesan = 'Update';
        }

        try {
            // cek kategori
            if (request()->kategori == 'post'){
                $kategori = 0;
            } elseif (request()->kategori == 'url'){
                $kategori = 1;
            }

            // status hak akses
            if (request()->hakakses == 1) {
                // cek validasi list user
                $validator = Validator::make(request()->all(), [
                    'listuser'      => "required",
                ]);

                // handle jika tidak terpenuhi
                if ($validator->fails()) {
                    $data_error['message']      = $validator->errors();
                    $data_error['error_code']   = 1; //error
                    return response()->json([
                        'status' => 0,
                        'data'  => $data_error
                    ], 200);            
                }
                
                $listuser   = request()->listuser;
            }

            // date
            date_default_timezone_set('Asia/Jakarta');
            $now = Carbon::now();
            $waktu = $now->year."".$now->month."".$now->day."".$now->hour."".$now->minute."".$now->second;

            $q = Forum::where('id', request()->id)->first();

            // memastikan apakah forum milik nya
            if (!isset($q->judul)) {
                $datas['message']    =   'Akses Ditolak';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $datas
                ],200);
            }elseif ($q->user_id <> Auth::user()->personal_number && Auth::user()->role <> 3) {
                $datas['message']    =   'Akses Ditolak';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $datas
                ],200);
            }

            // jika punya akses maka kita update datanya
            $up['judul']               =   (string)request()->judul;
            $up['content']             =   (string)request()->content;
            $up['kategori']            =   $kategori;
            $up['status']              =   request()->status; //Dari Draft bisa jadi di publish atau di save ulang
            $up['restriction']         =   request()->hakakses;
            $q->update($up);

            // restricted ?
            // clearing
            ForumUser::where('forum_id',request()->id)->delete();
            // update data forumnya
            if (request()->hakakses == 1) {
                for ($i=0; $i < count($listuser) ; $i++) { 
                    $simpanuser['forum_id']     =   request()->id;
                    $simpanuser['user_id']      =   (int)$listuser[$i];
                    ForumUser::create($simpanuser);
                }   
            }

            $data['message']        =   'Berhasil '.$pesan;
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'Proses '.$pesan.' gagal';
            $datas['errors']     =   $th;
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }

    public function listforum($search = '*'){
        if ($search ==  '*') {
            $data = Forum::with(['user', 'forumUser'])
            ->where(function($q){
                $q->where('restriction',0);//cek apakah public
                $q->orWhere(function($q2){//cek jika private terdaftar kah PN nya di whitelist
                    $q2->where('restriction',1);//cek apakah private
                    $q2->whereHas('forumUser', function ($query) {
                        return $query->where('user_id', Auth::user()->personal_number);
                    });
                });
                $q->orWhere('user_id',Auth::user()->personal_number);//cek apakah dia adalah pembuatnya
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
                $q->orWhere(function($q2){//cek jika private terdaftar kah PN nya di whitelist
                    $q2->where('restriction',1);//cek apakah private
                    $q2->whereHas('forumUser', function ($query) {
                        return $query->where('user_id', Auth::user()->personal_number);
                    });
                });
                $q->orWhere('user_id',Auth::user()->personal_number);//cek apakah dia adalah pembuatnya
            })
            ->where('status', '1')
            ->orderby('updated_at','desc')
            ->paginate(10);
        }
        
        $datas = [];
        foreach ($data as $item) {
            // try {
            //     if($item->kategori == 1){
            //         $embed = new Embed();
            //         $info = $embed->get($item->content);
            //         $previewlink = $info->image;
            //         $link        = $info->url;
            //     }else{
            //         $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
            //         $link        = "";
            //     }
            // } catch (\Throwable $th) {
            //     $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
            // }

            if($item->kategori == 1){
                $previewlink = config('app.FE_url').'assets/img/default-forum-link.png';
                $link        = $item->content;
            }else{
                $previewlink = "";
                $link        = "";
            }
            
            $object = new stdClass;
            $object->link               = $link;
            $object->thumbnail          = $previewlink;
            $object->kategori           = $item->kategori;
            $object->username           = $item->user->name;
            $object->avatar_id          = $item->user->avatar_id;
            $object->personal_number    = $item->user->personal_number;
            $object->updated_at         = $item->updated_at;
            $object->restriction        = $item->restriction;
            $object->slug               = $item->slug;
            $object->judul              = $item->judul;
            $object->content            = $item->content;
            $object->forumcomment       = $item->forumcomment; 
            $datas[]                    = $object;
        }

        // buat testing
        // return response()->json(['data' => $data]);
        if ($search == '*') { //ketika pencarian kosong
            $notFound   = false; //found
            $view       = view('Forum.list',compact('datas', 'notFound'))->render();
        } else {
            if(!$datas) { //ketika data kosong + pencarian ada
                $notFound   = true; //not found
                $view       = view('Forum.list',compact('datas', 'notFound'))->render();
            } else {
                $notFound   = false; //pencarian ada + data ada
                $view       = view('Forum.list',compact('datas', 'notFound'))->render();
            }
        }
        
        // $view       = view('Forum.list',compact('datas'))->render();
        $paginate   = view('Forum.paginate',compact('data'))->render();

        try {
            $resp['message']    =   'GET Berhasil.';
            $resp['html']       =   $view;
            $resp['paginate']   =   $paginate;
            $resp['datas']   =   $datas;
            $resp['search']   =   $search;
            $resp['notFound']   =   $notFound;
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

    public function hapus($id)
    {
        $q = Forum::where('user_id',Auth::user()->personal_number)->find($id);
        
        // cek milik nya dan ada forumnya ?
        if (!isset($q->judul) or Auth::user()->role !== 3) {
            $datas['message']    =   'Akses Ditolak';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
        
        try {
            $hide['status'] =   '2';
            $q->update($hide);
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

    public function detail($slug)
    {
        $query = Forum::withcount('forumcomment')->with(['user', 'forumcomment' => function($q){
            $q->where('parent_id',null);
            $q->orderby('created_at','desc');
            $q->with(['child' => function($q2){
                $q2->orderby('created_at','desc');
            }]);
        }])
        ->where('slug', $slug)
        ->where('status', '1') //status 1 == terbit
        ->orderby('created_at','desc')
        ->first();
        // return $query;

        $ceklevel   =   Level::where('xp', '<=',$query->user->xp)->orderby('xp','desc')->first();
        $level_id   =   $ceklevel->id;

        // Event Activity
        event(new TriggerActivityEvent(9, Auth::user()->personal_number));

        try {
            $data['message']        =   'GET Berhasil.';
            $data['data']           =   $query;
            $data['level']          =   $level_id;
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

    public function comment()
    {
        $validator = Validator::make(request()->all(), [
            'komentar'      => "required",
            'parent_form'   => 'nullable|numeric',
            'project_form'  => 'required|numeric',
            'reply_form'    => 'nullable|numeric',
        ]);

        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message']      = $validator->errors();
            $data_error['error_code']   = 1; //error
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 200);            
        }

        try {
            // create
            $add['parent_id']           =   request()->parent_form;
            $add['forum_id']            =   mb_strtolower(request()->project_form);
            $add['user_id']             =   Auth::user()->personal_number;
            $add['replyto_user_id']     =   request()->reply_form;
            $add['comment']             =   (string)request()->komentar;
            ForumComment::create($add);

            $sample                     = Forum::with(['user', 'forumcomment' => function($q){
                $q->where('parent_id',null);
                $q->orderby('created_at','desc');
                $q->with(['child' => function($q2){
                    $q2->orderby('created_at','desc');
                }]);
            }])->find(request()->forum_from);

            // Event Activity
            event(new TriggerActivityEvent(5, Auth::user()->personal_number));

            $data['message']        =   'Create Comment Berhasil.';
            $data['data']           =   $sample;
            return response()->json([
                "status"    => 1,
                "data"      => $data
            ],200);
        } catch (\Throwable $th) {
            $datas['message']    =   'Create Comment failed';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $datas
            ],200);
        }
    }
}