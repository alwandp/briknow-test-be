<?php

namespace App\Http\Controllers;

use App\FavoriteImplementation;
use App\MyComsup;
use Auth;

class FavoriteComSupportController extends Controller {

    public function index(){
        //
    }

    public function save($table, $id) {
        if ($table == 'content') {
            $fav = MyComsup::where('comsup_id', $id)->where('user_id', Auth::user()->id)->first();
        } else {
            $fav = FavoriteImplementation::where('imp_id', $id)->where('user_id', Auth::user()->id)->first();
        }

        if ($fav) { //hapus favorit jika ada
            try {
                $fav->delete();
                $data['kondisi']    =   0;
                $data['message']    =   "Data berhasil dihapus.";
                return response()->json([
                    "status"    =>  1,
                    "data"      => $data
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    "status"    =>  0,
                    "message"   =>  "Data gagal dihapus.",
                ],500);
            }
        } else { //add favorit jika belum
            try {
                if ($table == 'content') {
                    $favorit = new MyComsup;
                    $favorit->comsup_id = $id;
                } else {
                    $favorit = new FavoriteImplementation();
                    $favorit->imp_id = $id;
                }
                $favorit->user_id = Auth::user()->id;
                $favorit->save();

                $data['kondisi']    =   1;
                $data['message']    =   "Data Berhasil Ditambahkan.";
                return response()->json([
                    "status"    =>  1,
                    "data"      => $data
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    "status"    =>  0,
                    "message"   =>  "Data gagal ditambahkan.",
                ],500);
            }
        }
    }
}