<?php

namespace App\Http\Controllers;

use App\Consultant;
use Illuminate\Http\Request;
use Artisan;
use Validator;

class ManageConsultantController extends Controller
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
        //     Artisan::call('scout:import', ['model' => 'App\Consultant']);
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
            $q = Consultant::latest()->get();

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
                $data = Consultant::latest()->paginate(10);
            } else { //dengan search
                $data = Consultant::where(function($q) use ($search){
                    $q->orWhere('nama','like','%'.$search.'%');
                    $q->orWhere('tentang','like','%'.$search.'%');
                    $q->orWhere('bidang','like','%'.$search.'%');
                    $q->orWhere('website','like','%'.$search.'%');
                    $q->orWhere('telepon','like','%'.$search.'%');
                    $q->orWhere('email','like','%'.$search.'%');
                })->paginate(10); //Search tanpa filter samsek
            }
            
        } else if ($sort <> "*" && $sort2 == "*") { //sort1 only
            if ($search == "*") { //sort only
                if ($sort == 'asc') { //SORT == NEWEST
                    $data = Consultant::latest()->paginate(10);
                } else { //SORT == OLDEST
                    $data = Consultant::oldest()->paginate(10);
                }
            } else { //sort + search
                if ($sort == 'asc') { //SORT == NEWEST
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->latest()->paginate(10);
                } else { //SORT == OLDEST
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->oldest()->paginate(10);
                }
            }
        } else if ($sort == "*" && $sort2 <> "*") { //sort ga ada, sort 2 ada
            if ($search == "*") { //sort2 only
                if ($sort2 == 'asc') { //SORT == A-Z
                    $data = Consultant::orderBy('nama', 'asc')->paginate(10);
                } else { //SORT == Z-A
                    $data = Consultant::orderBy('nama', 'desc')->paginate(10);
                }
            } else { //sort2 + search
                if ($sort2 == 'asc') { //SORT == A-Z
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->orderBy('nama', 'asc')->paginate(10);
                } else { //SORT == Z-A
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->orderBy('nama', 'desc')->paginate(10);
                }
            }

        } else { //sort dua duanya ada
            if ($search == "*") { //sort + sort2 only
                if ($sort == 'asc' && $sort2 == 'asc') { //SORT == NEW + A-Z
                    $data = Consultant::orderBy('nama', 'asc')->latest()->paginate(10);
                } else if ($sort == 'asc' && $sort2 == 'desc') { //SORT == NEW + Z-A
                    $data = Consultant::orderBy('nama', 'asc')->oldest()->paginate(10);
                } else if ($sort == 'desc' && $sort2 == 'asc') { //SORT == OLD + A-Z
                    $data = Consultant::orderBy('nama', 'desc')->latest()->paginate(10);
                } else { //SORT == OLD + Z-A
                    $data = Consultant::orderBy('nama', 'desc')->oldest()->paginate(10);
                }
            } else { //sort + sort2 + search
                if ($sort == 'asc' && $sort2 == 'asc') { //SORT == NEW + A-Z
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->orderBy('nama', 'asc')->latest()->paginate(10);
                } else if ($sort == 'asc' && $sort2 == 'desc') { //SORT == NEW + Z-A
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->orderBy('nama', 'asc')->oldest()->paginate(10);
                } else if ($sort == 'desc' && $sort2 == 'asc') { //SORT == OLD + A-Z
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->orderBy('nama', 'desc')->latest()->paginate(10);
                } else { //SORT == OLD + Z-A
                    $data = Consultant::where(function($q) use ($search){
                        $q->orWhere('nama','like','%'.$search.'%');
                        $q->orWhere('tentang','like','%'.$search.'%');
                        $q->orWhere('bidang','like','%'.$search.'%');
                        $q->orWhere('website','like','%'.$search.'%');
                        $q->orWhere('telepon','like','%'.$search.'%');
                        $q->orWhere('email','like','%'.$search.'%');
                    })->orderBy('nama', 'desc')->oldest()->paginate(10);
                }
                
            }

        }

        $data->map(function ($key, $value) {
            $key->lokasi =  \Str::limit($key->lokasi, 25,'..');
        });

        $view       = view('manage_consultant.list',compact('data'))->render();
        $paginate   = view('manage_consultant.paginate',compact('data'))->render();

        $data['message'] = 'GET Berhasil.';
        $data['html'] = $view;
        $data['paginate'] = $paginate;
        return response()->json([
            "status"    => 1,
            "data"      => $data,
        ],200);
      } catch (\Throwable $th) {
          $data['message'] = 'GET Gagal'.$th;
          return response()->json([
              'status'    =>  0,
              'data'      =>  $data
          ],200);
      }
    }

    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'nama'         => "required",
            'bidang'       => "required",
            'website'      => "required",
            'telepon'      => "required|min:10|max:15",
            'email'        => "required|email",
            'tentang'      => "required",
            'lokasi'       => "required",
        ]);

        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message'] = $validator->errors();
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 200);
        }

        try {
            $q = Consultant::create([
                'nama'         => request()->nama,
                'bidang'       => request()->bidang,
                'website'      => request()->website,
                'telepon'      => request()->telepon,
                'email'        => request()->email,
                'instagram'    => request()->instagram??"-",
                'facebook'     => request()->facebook??"-",
                'tentang'      => request()->tentang,
                'lokasi'       => request()->lokasi,
            ]);

            // refresh ES
            // $this->reloadES();
            $data['message'] = 'Create Consultant Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'Create Consultant Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function detail($id)
    {
        $query = Consultant::find($id); //dibatasi yg dikirim ke fe hanya jenis user dan hak akses*

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

    public function update($id)
    {
        $validator = Validator::make(request()->all(), [
            'nama_edit'         => "required",
            'bidang_edit'       => "required",
            'website_edit'      => "required",
            'telepon_edit'      => "required|min:10|max:15",
            'email_edit'        => "required|email",
            'tentang_edit'      => "required",
            'lokasi_edit'       => "required",
        ]);

        // handle jika tidak terpenuhi
        if ($validator->fails()) {
            $data_error['message'] = $validator->errors();
            return response()->json([
                'status' => 0,
                'data'  => $data_error
            ], 200);
        }

        try {
            $q = Consultant::find($id);
            $up['nama']         = request()->nama_edit;
            $up['bidang']       = request()->bidang_edit;
            $up['website']      = request()->website_edit;
            $up['telepon']      = request()->telepon_edit;
            $up['email']        = request()->email_edit;
            $up['instagram']    = request()->instagram_edit??'-';
            $up['facebook']     = request()->facebook_edit??'-';
            $up['tentang']      = request()->tentang_edit;
            $up['lokasi']       = request()->lokasi_edit;

            $q->update($up);
            // refresh ES
            // $this->reloadES();

            $data['message'] = 'Update Consultant Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'Update Consultant Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    public function hapus($id)
    {
        $q = Consultant::find($id);
        
        try {
            $q->delete();
            // refresh ES
            // $this->reloadES();

            $data['message'] = 'Delete Consultant Berhasil.';
            return response()->json([
                "status"    => 1,
                "data"      => $data,
            ],200);
        } catch (\Throwable $th) {
            $data['message'] = 'Delete Consultant Gagal';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }
}