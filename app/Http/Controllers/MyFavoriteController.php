<?php

namespace App\Http\Controllers;

use App\CommunicationSupport;
use App\User;
use Auth;
use Illuminate\Http\Request;
use stdClass;

class MyFavoriteController extends Controller
{
    public function getFavs()
    {
        $query = User::with(['favorite_consultant', 'favorite_project'])
        ->where('id', Auth::user()->id)->first();

        $favs = array();

        foreach ($query->favorite_project as $favpro) {
            $favp = new stdClass;
            $favp->id = $favpro->id;
            $favp->user_id = $favpro->user_id;
            $favp->kategori_id = $favpro->project_id;
            $favp->created_at = $favpro->created_at;
            $favp->updated_at = $favpro->updated_at;
            $favp->kategori = "Project";
            $favs[] = $favp;
        }

        foreach ($query->favorite_consultant as $favcon) {
            $favc = new stdClass;
            $favc->id = $favcon->id;
            $favc->user_id = $favcon->user_id;
            $favc->kategori_id = $favcon->consultant_id;
            $favc->created_at = $favcon->created_at;
            $favc->updated_at = $favcon->updated_at;
            $favc->kategori = "Consultant";
            $favs[] = $favc;
        }

        try {
            $data['data']    =   $query;
            $data['favs']    =   $favs;
            $data['message']    =   "GET Data Berhasil.";
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                "status"    =>  0,
                "message"   =>  "GET Data Gagal.",
            ],500);
        }
    }

    public function fav_proj($sort = "asc")
    {
        $query = User::with(['favorite_project' => function($data) use ($sort) {
            $data->with(['project' => function($data2) use ($sort) {
                $data2->orderBy('nama', $sort);
            }]);
        }])->where('id', Auth::user()->id)->first();

        $favs = array();

        foreach ($query->favorite_project as $favpro) {
            $favp = new stdClass;
            $favp->id = $favpro->id;
            $favp->user_id = $favpro->user_id;
            $favp->kategori_id = $favpro->project_id;
            $favp->created_at = $favpro->created_at;
            $favp->updated_at = $favpro->updated_at;
            $favp->kategori = "Project";
            $favs[] = $favp;
        }

        try {
            $data['data']    =   $query;
            $data['favs']    =   sort($favs);
            $data['message']    =   "GET Data Berhasil.";
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                "status"    =>  0,
                "message"   =>  "GET Data Gagal.",
            ],500);
        }
    }

    public function fav_cons($sort = "asc")
    {
        $query = User::with(['favorite_consultant' => function($data) use ($sort) {
            $data->with(['consultant'=> function($data2) use ($sort) {
                $data2->orderBy('nama', $sort);
            }]);
        }])->where('id', Auth::user()->id)->first();

        $favs = array();

        foreach ($query->favorite_consultant as $favcon) {
            $favc = new stdClass;
            $favc->id = $favcon->id;
            $favc->user_id = $favcon->user_id;
            $favc->kategori_id = $favcon->consultant_id;
            $favc->created_at = $favcon->created_at;
            $favc->updated_at = $favcon->updated_at;
            $favc->kategori = "Consultant";
            $favs[] = $favc;
        }

        try {
            $data['data']    =   $query;
            if ($sort == 'asc') {
                $data['favs']    =   sort($favs);
            }else{
                $data['favs']    =   rsort($favs);
            }
            $data['message']    =   "GET Data Berhasil.";
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                "status"    =>  0,
                "message"   =>  "GET Data Gagal.",
            ],500);
        }
    }

    public function fav_com($sort = "asc")
    {
        $query = CommunicationSupport::whereHas('favorite_com', function($q) {
            $q->where('user_id', Auth::user()->id);
        })->orderBy('title', $sort)->get();

        try {
            $data['data']    =   $query;
            $data['message']    =   "GET Data Berhasil.";
            return response()->json([
                "status"    =>  1,
                "data"      => $data
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                "status"    =>  0,
                "message"   =>  "GET Data Gagal.",
            ],500);
        }
    }
}
