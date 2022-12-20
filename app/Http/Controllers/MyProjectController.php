<?php

namespace App\Http\Controllers;

use App\MyProject;
use App\Project;
use Auth;
use Illuminate\Http\Request;

class MyProjectController extends Controller
{
    public function index()
    {
        // if (Auth::User()->role == 0) {
        //     $query = Project::where('user_maker', Auth::user()->personal_number)->orderBy('created_at', 'DESC')->get();
        // }elseif (Auth::User()->role == 1) {
        //     $query = Project::where('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1)->orderBy('created_at', 'DESC')->get();
        // }elseif (Auth::User()->role == 2) {
        //     $query = Project::where('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2)->orderBy('created_at', 'DESC')->get();
        // }elseif (Auth::User()->role == 3) {
        //     $temp = [3,4,5,6];
        //     $query = Project::whereIn('flag_mcs', $temp)->orderBy('created_at', 'DESC')->get();
        // }

        if (Auth::User()->role == 0) {
            $query = Project::with(['consultant','divisi','keywords', 'lesson_learned','project_managers', 'document'])->where(function($q){
                $q->orWhere('user_maker', Auth::user()->personal_number);
                $q->orWhere('user_checker', Auth::user()->personal_number)->where('flag_mcs', 1);
                $q->orWhere('user_signer', Auth::user()->personal_number)->where('flag_mcs', 2);
            })->orderBy('created_at', 'DESC')->get();
        }elseif (Auth::User()->role == 3) {
            $temp = [3,4,5,6];
            $query = Project::with(['consultant','divisi','keywords', 'lesson_learned','project_managers', 'document'])->whereIn('flag_mcs', $temp)->orderBy('created_at', 'DESC')->get();
        }

        try {
            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $query;
            return response()->json([
                "status"    => 1,
                "data"      => $data
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