<?php

namespace App\Http\Controllers;

use App\Divisi;
use App\Project;
use App\User;
use Artisan;
use Auth;
use Illuminate\Http\Request;
use Validator;

class ManageUkerController extends Controller
{
    public $sort;

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

    public function index()
    {
        try {
            $q = Divisi::latest()->get();

            // siapkan response
            $data['message'] = 'GET Berhasil.';
            $data['bahan'] = $q;

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

    public function index_content($sort="*", $sort2="*", $search="*")
    {
      try {
        //SORT 1 = New/Old
        //SORT 2 = AZ/ZA
        $search = str_replace("_"," ",$search);
        if ($sort == "*" && $sort2 == "*") { //sort + sort2 ga ada
            if ($search == "*") { //tanpa search
                $data = Divisi::latest()->paginate(10);
                $who = "Kena Search kosong + Filter Kosong Polosan";
            } else { //dengan search
                $data = Divisi::where(function($q) use ($search){
                    $q->orWhere('divisi','like','%'.$search.'%');
                    $q->orWhere('direktorat','like','%'.$search.'%');
                    $q->orWhere('cost_center','like','%'.$search.'%');
                    $q->orWhere('shortname','like','%'.$search.'%');
                })->paginate(10); //Search tanpa filter samsek
                $who = "NO FILTER + KENA SEARCH";
            }

        } else if ($sort <> "*" && $sort2 == "*") { //sort1 only
            if ($search == "*") { //sort only
                if ($sort == 'asc') { //SORT == NEWEST
                    $data = Divisi::latest()->paginate(10);
                    $who = "KENA FILTER TERBARU TANPA SEARCH";
                } else { //SORT == OLDEST
                    $data = Divisi::oldest()->paginate(10);
                    $who = "KENA FILTER TERLAMA TANPA SEARCH";
                }
            } else { //sort + search
                if ($sort == 'asc') { //SORT == NEWEST
                    $who = "KENA FILTER TERBARU + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->latest()->paginate(10);
                } else { //SORT == OLDEST
                    $who = "KENA FILTER TERLAMA + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->oldest()->paginate(10);
                }
            }
        } else if ($sort == "*" && $sort2 <> "*") { //sort ga ada, sort 2 ada
            if ($search == "*") { //sort2 only
                if ($sort2 == 'asc') { //SORT == A-Z
                    $who = "KENA FILTER A-Z TANPA SEARCH";
                    $data = Divisi::orderBy('direktorat', 'asc')->paginate(10);
                } else { //SORT == Z-A
                    $who = "KENA FILTER Z-A TANPA SEARCH";
                    $data = Divisi::orderBy('direktorat', 'desc')->paginate(10);
                }
            } else { //sort2 + search
                if ($sort2 == 'asc') { //SORT == A-Z
                    $who = "KENA FILTER A-Z + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->orderBy('direktorat', 'asc')->paginate(10);
                } else { //SORT == Z-A
                    $who = "KENA FILTER Z-A + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->orderBy('direktorat', 'desc')->paginate(10);
                }
            }

        } else { //sort dua duanya ada
            if ($search == "*") { //sort + sort2 only
                if ($sort == 'asc' && $sort2 == 'asc') { //SORT == NEW + A-Z
                    $who = "KENA FILTER TERBARU + FILTER A-Z TANPA SEARCH";
                    $data = Divisi::orderBy('direktorat', 'asc')->latest()->paginate(10);
                } else if ($sort == 'asc' && $sort2 == 'desc') { //SORT == NEW + Z-A
                    $who = "KENA FILTER TERBARU + FILTER Z-A TANPA SEARCH";
                    $data = Divisi::orderBy('direktorat', 'asc')->oldest()->paginate(10);
                } else if ($sort == 'desc' && $sort2 == 'asc') { //SORT == OLD + A-Z
                    $who = "KENA FILTER TERLAMA + FILTER A-Z TANPA SEARCH";
                    $data = Divisi::orderBy('direktorat', 'desc')->latest()->paginate(10);
                } else { //SORT == OLD + Z-A
                    $who = "KENA FILTER TERLAMA + FILTER Z-A TANPA SEARCH";
                    $data = Divisi::orderBy('direktorat', 'desc')->oldest()->paginate(10);
                }
            } else { //sort + sort2 + search
                if ($sort == 'asc' && $sort2 == 'asc') { //SORT == NEW + A-Z
                    $who = "KENA FILTER TERBARU + FILTER A-Z + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->orderBy('direktorat', 'asc')->latest()->paginate(10);
                } else if ($sort == 'asc' && $sort2 == 'desc') { //SORT == NEW + Z-A
                    $who = "KENA FILTER TERBARU + FILTER Z-A + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->orderBy('direktorat', 'asc')->oldest()->paginate(10);
                } else if ($sort == 'desc' && $sort2 == 'asc') { //SORT == OLD + A-Z
                    $who = "KENA FILTER TERLAMA + FILTER A-Z + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->orderBy('direktorat', 'desc')->latest()->paginate(10);
                } else { //SORT == OLD + Z-A
                    $who = "KENA FILTER TERLAMA + FILTER Z-A + SEARCH";
                    $data = Divisi::where(function($q) use ($search){
                        $q->orWhere('divisi','like','%'.$search.'%');
                        $q->orWhere('direktorat','like','%'.$search.'%');
                        $q->orWhere('cost_center','like','%'.$search.'%');
                        $q->orWhere('shortname','like','%'.$search.'%');
                    })->orderBy('direktorat', 'desc')->oldest()->paginate(10);
                }

            }

        }

        // return response()->json($data);
        $view       = view('manage_uker.list',compact('data'))->render();
        $paginate   = view('manage_user.paginate',compact('data'))->render();

        $data['message'] = 'GET Berhasil.';
        $data['html'] = $view;
        $data['paginate'] = $paginate;
        $data['kena_siapa'] = $who;
        return response()->json([
            "status"    => 1,
            "data"      => $data,
        ],200);
      } catch (\Throwable $th) {
        $data['kena_siapa'] = $who;
        $data['message'] = 'GET Gagal'.$th;
        return response()->json([
            'status'    =>  0,
            'data'      =>  $data
        ],200);
      }
    }

    public function create()
    {
        try {
            // handle direktorat
            $validator = Validator::make(request()->all(), [
                'cost_center'         => "required|max:7|unique:divisis,cost_center",
                'divisi'              => "required",
                'shortname'           => "required|max:4",
            ]);

            // handle jika tidak terpenuhi
            if ($validator->fails()) {
                $data_error['message'] = $validator->errors();
                return response()->json([
                    'status' => 0,
                    'data'  => $data_error
                ], 200);
            }
            // jika ter-set makasimpan
            // jika tidak ada di kategorikan ke "Lainnya"
            if (isset(request()->direktorat)) {
                if (request()->direktorat == "") {
                    $direktorat = NULL;
                }else{
                    $direktorat = request()->direktorat;
                }
            }else{
                $direktorat = NULL;
            }

            $q = Divisi::create([
                'cost_center'            => request()->cost_center,
                'direktorat'             => $direktorat,
                'divisi'                 => request()->divisi,
                'shortname'              => request()->shortname,
            ]);
            // refresh ES
            //$this->reloadES();
            $data['message'] = 'Create Divisi Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'Create Divisi Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function update($id)
    {
        // handle direktorat
        $validator = Validator::make(request()->all(), [
            'cost_center_edit'         => "required|max:7|unique:divisis,cost_center,".$id,
            'divisi_edit'              => "required",
            'shortname_edit'           => "required|max:4",
        ]);

        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message'] = $validator->errors();
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 200);
        }

        // handle direktorat
        // jika ter-set makasimpan
        // jika tidak ada di kategorikan ke "Lainnya"
        if (isset(request()->direktorat_edit)) {
            if (request()->direktorat_edit == "") {
                $direktorat = NULL;
            }else{
                $direktorat = request()->direktorat_edit;
            }
        }else{
            $direktorat = NULL;
        }

        $q = Divisi::find($id);
        $up['cost_center']  = request()->cost_center_edit;
        $up['direktorat']   = $direktorat;
        $up['divisi']       = request()->divisi_edit;
        $up['shortname']    = request()->shortname_edit;

        try {
            $q->update($up);
            // refresh ES
            //$this->reloadES();

            $data['message'] = 'Update Divisi Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'Update Divisi Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function hapus($id)
    {
        if ($id == 11111111) {
            $data['message'] = 'Tidak dapat dihapus';
            return response()->json([
                "status"    => -2,
                "data"      => $data,
            ],200);
        } else {
            $cekUserkuKenakah = User::where('divisi',$id)->where('personal_number', Auth::user()->personal_number)->count();
            Project::where('divisi_id', $id)->update([
                'divisi_id' => 11111111
            ]);
            User::where('divisi', $id)->update([
                'divisi' => 11111111
            ]);

            $q = Divisi::find($id);
            try {
                $q->delete();
                // refresh ES
                //$this->reloadES();
                $data['message'] = 'Delete Divisi Berhasil.';
                return response()->json([
                    "status"        => 1,
                    "data"          => $data,
                    "is_changed"    => $cekUserkuKenakah
                ],200);
            } catch (\Throwable $th) {
                $data['message'] = 'Delete Divisi Gagal';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $data
                ],200);
            }
        }
    }

    public function detail($id)
    {
        $query = Divisi::find($id); //dibatasi yg dikirim ke fe hanya jenis user dan hak akses*

        try {
            $data['message']    =   'GET Berhasil.';
            $data['data']       =   $query;
            return response()->json([
                "status"    => 1,
                "data"      => $data,
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
