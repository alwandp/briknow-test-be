<?php

namespace App\Http\Controllers;

use App\Divisi;
use App\Notifikasi;
use App\Project;
use App\UserAchievement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // notifikasi section
        $query          = Notifikasi::with('user')
        ->where(function($q){
            $q->orWhereDate('created_at','>=', Carbon::now()->subDays(3)->toDateTimeString());
            $q->orWhere('status', '0');
        })
        ->where('user_id',Auth::user()->personal_number)
        ->orderby('created_at','desc')
        ->get();

        $unread         = Notifikasi::with('user')
        ->where(function($q){
            $q->whereDate('created_at', Carbon::now()->subDays(3));
            $q->orWhere('status', '0');
        })
        ->where('status', '0')
        ->where('user_id',Auth::user()->personal_number)
        ->count();

        // Congratulation section
        $congrats          = UserAchievement::where('personal_number',Auth::user()->personal_number)
        ->where('congrats_view',0)
        ->orderby('created_at','asc')
        ->first();

        // divisi
        $divisi             =   Divisi::get();
        
        // direktorat
        $direktorat         =   Divisi::select('direktorat')->groupby('direktorat')->get();

        try {
            $data['message']        =   'Get Data Berhasil.';
            $data['data']           =   $query;
            $data['unread']         =   $unread;
            $data['congrats']       =   $congrats;
            $data['divisi']         =   $divisi;
            $data['direktorat']     =   $direktorat;
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

    public function indexproject()
    {
        $queryproject   = Project::without(['project_managers','divisi','keywords', 'consultant','lesson_learned','comment','usermaker','userchecker','usersigner'])->where(function($q){
            $q->orWhere('status_read', null);
            $q->orWhere('status_read', 0);
        })->whereIn('flag_mcs', [3,4,5])->count();

        try {
            $data['message']        =   'Get Data Berhasil.';
            $data['data']           =   $queryproject;  
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\notifikasi  $notifikasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, notifikasi $notifikasi)
    {
        $temp['status'] =   1;
        $query          =   Notifikasi::where('user_id',Auth::user()->personal_number)->update($temp);

        try {
            $data['message']        =   'Update Notifikasi Berhasil.';
            $data['data']           =   $query;  
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message']    =   'Update Notifikasi Gagal';
            $data['data']       =   0;
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }
}
