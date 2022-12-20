<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\TriggerActivityEvent;
use App\Forum;
use App\ForumComment;
use App\Notifikasi;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function create()
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
            // cek ada project nya kah ??
            $sample                     = Project::with(['consultant', 'document', 'favorite_project','comment' => function($q){
                $q->where('parent_id',null);
                $q->orderby('created_at','desc');
                $q->with(['child' => function($q2){
                    $q2->orderby('created_at','desc');
                }]);
            }])->find(request()->project_form);

            if (!isset($sample->id)) {
                $datas['message']    =   'Project Tidak Ditemukan';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $datas
                ],200);
            }


            // create
            $add['parent_id']           =   request()->parent_form;
            $add['project_id']          =   mb_strtolower(request()->project_form);
            $add['user_id']             =   Auth::user()->personal_number;
            $add['replyto_user_id']     =   request()->reply_form;
            $add['comment']             =   (string)request()->komentar;
            $comment = Comment::create($add);

            // notifikasi
            if (request()->reply_form == null) {
                if ($sample->user_maker <> Auth::user()->personal_number) {
                    $Notifikasi        = Notifikasi::create([
                        'user_id'      =>  $sample->user_maker,
                        'kategori'     =>  2,
                        'judul'        =>  'Komentar Project',
                        'pesan'        =>  "<b>".$comment->user->name."</b> telah mengomentari Anda di Project <b>".$sample->nama."</b>",
                        'direct'       =>  config('app.FE_url')."project/".$sample->slug,
                        'status'       =>  0,
                    ]);
                }
            }else{
                if ($sample->user_maker <> Auth::user()->personal_number) {
                    $Notifikasi         = Notifikasi::create([
                        'user_id'      =>  $comment->Reply_user->personal_number,
                        'kategori'     =>  2,
                        'judul'        =>  'Komentar Project',
                        'pesan'        =>  "<b>".$comment->user->name."</b> telah Menanggapi Komentar Anda di Project <b>".$sample->nama."</b>",
                        'direct'       =>  config('app.FE_url')."project/".$sample->slug,
                        'status'       =>  0,
                    ]); 
                }
            }

            $sample                     = Project::with(['consultant', 'document', 'favorite_project','comment' => function($q){
                $q->where('parent_id',null);
                $q->orderby('created_at','desc');
                $q->with(['child' => function($q2){
                    $q2->orderby('created_at','desc');
                }]);
            }])->find(request()->project_form);
            
            // Event Activity
            event(new TriggerActivityEvent(4, Auth::user()->personal_number));

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

    public function createforum()
    {
        $validator = Validator::make(request()->all(), [
            'komentar'      => "required",
            'parent_form'   => 'nullable|numeric',
            'forum_form'  => 'required|numeric',
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
            // cek ada forum nya kah ?
            $sample = Forum::with(['user', 'forumcomment' => function($q){
                $q->where('parent_id',null);
                $q->orderby('created_at','desc');
                $q->with(['child' => function($q2){
                    $q2->orderby('created_at','desc');
                }]);
            }])
            ->where('status', '1') //status 1 == terbit
            ->orderby('created_at','desc')
            ->find(request()->forum_form);

            if (!isset($sample->id)) {
                $datas['message']    =   'Forum Tidak Ditemukan';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $datas
                ],200);
            }

            // create
            $add['parent_id']           =   request()->parent_form;
            $add['forum_id']            =   mb_strtolower(request()->forum_form);
            $add['user_id']             =   Auth::user()->personal_number;
            $add['replyto_user_id']     =   request()->reply_form;
            $add['comment']             =   (string)request()->komentar;
            $comment = ForumComment::create($add);

            // renew data
            $sample = Forum::with(['user', 'forumcomment' => function($q){
                $q->where('parent_id',null);
                $q->orderby('created_at','desc');
                $q->with(['child' => function($q2){
                    $q2->orderby('created_at','desc');
                }]);
            }])
            ->where('status', '1') //status 1 == terbit
            ->orderby('created_at','desc')
            ->find(request()->forum_form);

            // notifikasi
            if (request()->reply_form == null) {
                if ($sample->user->personal_number <> Auth::user()->personal_number) {
                    $Notifikasi        =   Notifikasi::create([
                        'user_id'      =>  $sample->user->personal_number,
                        'kategori'     =>  3,
                        'judul'        =>  'Komentar Forum',
                        'pesan'        =>  "<b>".$comment->user->name."</b> telah mengomentari Forum <b>".$sample->judul."</b>",
                        'direct'       =>  config('app.FE_url')."forum/".$sample->slug,
                        'status'       =>  0,
                    ]);
                }
            }else{
                if ($sample->user->personal_number <> Auth::user()->personal_number) {
                    $Notifikasi        =   Notifikasi::create([
                        'user_id'      =>  $comment->Reply_user->personal_number,
                        'kategori'     =>  3,
                        'judul'        =>  'Komentar Forum',
                        'pesan'        =>  "<b>".$comment->user->name."</b> telah Menanggapi Komentar Anda di Forum <b>".$sample->judul."</b>",
                        'direct'       =>  config('app.FE_url')."forum/".$sample->slug,
                        'status'       =>  0,
                    ]);
                }
            }

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