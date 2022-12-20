<?php

namespace App\Http\Controllers\Auth;

use App\Divisi;
use App\Events\TriggerActivityEvent;
use App\Http\Controllers\Controller;
use App\Level;
use App\User;
use App\User_log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Support\Facades\Hash;
use Session;
use SoapClient;
use SoapFault;

class LoginController extends Controller
{
    // handle portal addin
    public function login_addin(Request $request){
        // validator
        $tampung = $request->all();
        $validator = Validator::make($tampung,[
            'personal_number'    =>  'required|numeric',
            'katasandi'          =>  'required',
            'ip'                 =>  'required'
        ]);

        // checking Validator
        if ($validator->fails()) {
            $data['message']    =   $validator->errors();
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }

        try {
            //hit SOAP LDAP
            $hitLDAP = $this->LDAP(request()->personal_number, request()->katasandi);

            //sukses maka :
            $ResponseCostcenter     = '';
            $Responsenama = '';
            $hitLDAP = true;
            if ($hitLDAP !== false) {
                $data = User::where('personal_number', $request->personal_number)->first();
                if (!empty($data)){
                    //setelah save loginkan otomatis tanpa password
                    $datauser['ip_address']       =   request()->ip;
                    try {
                        $position = \Location::get($datauser['ip_address']);
                    } catch (\Throwable $th) {}
                    $datauser['user_id']          =   $data->id;
                    $datauser['country_name']     =   $position->countryName??null;
                    $datauser['country_code']     =   $position->countryName??null;
                    $datauser['region_code']      =   $position->regionCode??null;
                    $datauser['city_name']        =   $position->cityName??null;
                    $datauser['zip_code']         =   $position->zipCode??null;
                    $datauser['latitude']         =   $position->latitude??null;
                    $datauser['longitude']        =   $position->longitude??null;
                    $datauser['area_code']        =   $position->areaCode??null;
                    User_log::create($datauser);

                    // Event Activity
                    event(new TriggerActivityEvent(6, $request->personal_number));

                    // return
                    $data->tokens()->delete();
                    $kirim['personal_number']  = $data->personal_number;
                    $kirim['email']            = $data->email;
                    $kirim['username']         = $data->username;
                    $kirim['token']            = $data->createToken('web-token')->plainTextToken;
                    return response()->json([
                        "status"    =>  1,
                        "data"      =>  $kirim
                    ],200);
                }else{
                    // -----------------------------
                    // tempat API Auth
                    // -----------------------------
                        // init hasil
                        $access_token = '';
                        // ---------------
                        $ch = curl_init();
                        $headers  = [
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                                ];
                        $postData = [
                            'client_id'     => 'briknow',
                            'client_secret' => 'fa14f443b2433ecc7a3091942aff8c41fdc92a90',
                            'scope'         => 'public',
                            'grant_type'    => 'client_credentials',
                        ];
                        curl_setopt($ch, CURLOPT_URL,config('app.api_auth_bristar'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result     = curl_exec ($ch);
                        $hasil      = json_decode($result);
                        if (isset($hasil->access_token)) {
                            $access_token = $hasil->access_token;

                            // -----------------------------
                            // tempat API Detail Proyek
                            // -----------------------------
                            $ch2 = curl_init();
                            $headers  = [
                                        'Content-Type: application/json',
                                        'Accept: application/json',
                                        "Authorization: Bearer $access_token",
                                    ];
                            $postData = [
                                'pernr'     => $request->personal_number,
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
                                $ResponseCostcenter = $hasil2->responseData->COST_CENTER;
                                $Responsenama       = $hasil2->responseData->NAMA;
                            }else{
                                $msg['message'] =   'Akun Tidak Ditemukan';
                                return response()->json([
                                    "status"    =>  0,
                                    "data"      =>  $msg
                                ],200);
                            }
                        }else{
                            $msg['message'] =   'Get User Failed';
                            return response()->json([
                                "status"    =>  0,
                                "data"      =>  $msg
                            ],200);
                        }
                    // -----------------------------

                    //kita perlu hasil kembalian API Bristar disini untuk dilakukan REGISTRASI AKUN yg belum Terdaftar tapi Personal Number valid
                    //daftarkan jika kembalian LDAP tidak menemukan
                    $dataDivisi = Divisi::where('cost_center', $ResponseCostcenter)->first();
                    // add user
                    $new                    = new User();
                    $new->name              = $Responsenama??'User';
                    $new->personal_number   = $request->personal_number;
                    $new->username          = $Responsenama??'User';
                    $new->email             = $hitLDAP;
                    $new->last_login_at     = Carbon::now();
                    $new->role              = 0;
                    $new->divisi            = isset($dataDivisi->id)? $dataDivisi->id : 11111111;
                    $new->xp                = 0;
                    $new->avatar_id         = 1;
                    $new->save();

                    //setelah save loginkan otomatis tanpa password
                    $cek_user_baru = User::where('personal_number',$request->personal_number)->first();
                    $datauser['ip_address']       =   request()->ip;
                    try {
                        $position = \Location::get($datauser['ip_address']);
                    } catch (\Throwable $th) {}
                    $datauser['user_id']          =   $cek_user_baru->id;
                    $datauser['country_name']     =   $position->countryName??null;
                    $datauser['country_code']     =   $position->countryName??null;
                    $datauser['region_code']      =   $position->regionCode??null;
                    $datauser['city_name']        =   $position->cityName??null;
                    $datauser['zip_code']         =   $position->zipCode??null;
                    $datauser['latitude']         =   $position->latitude??null;
                    $datauser['longitude']        =   $position->longitude??null;
                    $datauser['area_code']        =   $position->areaCode??null;
                    User_log::create($datauser);

                    // Event Activity
                    event(new TriggerActivityEvent(6, $cek_user_baru->personal_number));

                    // return
                    $cek_user_baru->tokens()->delete();
                    $kirim['id']                 =   $cek_user_baru->id;
                    $kirim['avatar_id']          =   $cek_user_baru->avatar_id;
                    $kirim['exp']                =   $cek_user_baru->xp;
                    $kirim['name']               =   $cek_user_baru->name;
                    $kirim['personal_number']    =   $cek_user_baru->personal_number;
                    $kirim['username']           =   $cek_user_baru->username;
                    $kirim['email']              =   $cek_user_baru->email;
                    // $kirim['is_admin']           =   $cek_user_baru->is_admin;
                    $kirim['direktorat']         =   $cek_user_baru->divisis->id??11111111;
                    $kirim['divisi']             =   $cek_user_baru->divisis->id??11111111;
                    $kirim['last_login_at']      =   $cek_user_baru->last_login_at;
                    $kirim['role']               =   $cek_user_baru->role;
                    $kirim['login_at']           =   Carbon::now();
                    $kirim['token']              =   $cek_user_baru->createToken('web-token')->plainTextToken;

                    return response()->json([
                        "status"    =>  1,
                        "data"      =>  $kirim
                    ],200);
                }
            }else{
                $data['message']    =   'Personal Number / Password Salah';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $data
                ],200);
            }
        } catch (\Throwable $th) {
            $data['message']    =   'Something Wrong';
            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
    }

    // handle portal briknow
    public function login_web(Request $request){
	$access_token = '';
		$ch = curl_init();
		$headers = [
				'Content-Type: application/json',
				'Accept: application/json',
			];
		$postData = [
				'client_id' => 'briknow',
				'client_secret' => 'fa14f443b2433ecc7a3091942aff8c41fdc92a90',
				'scope' => 'public',
				'grant_type' => 'client_credentials',
			];
			curl_setopt($ch, CURLOPT_URL,config('app.api_auth_bristar'));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($ch);
			$hasil = json_decode($result);
			if (isset($hasil->access_token)){$access_token = $hasil->access_token;}

        // validator
        $tampung = $request->all();
        $validator = Validator::make($tampung,[
            'personal_number'   =>  'required|numeric',
            'katasandi'         =>  'required',
            'ip'                =>  'required'
        ]);

        // checking Validator
        if ($validator->fails()) {
            $data['validator']    =   $validator->errors();

            return response()->json([
                'status'    =>  0,
                'data'      =>  $data
            ],200);
        }
//            hit SOAP LDAP
            $hitLDAP = $this->LDAP(request()->personal_number, request()->katasandi);
/*            $data           = User::where('personal_number', $request->personal_number)->first();
            $data->tokens()->delete();
            $kirim['id']                 =   $data->id;
            $kirim['avatar_id']          =   $data->avatar_id;
            $kirim['exp']                =   $data->xp;
//            $kirim['level_id']           =   $cekleveling->id;
//            $kirim['level_name']         =   $cekleveling->name;
            $kirim['name']               =   $data->name;
            $kirim['personal_number']    =   $data->personal_number;
            $kirim['username']           =   $data->username;
            $kirim['email']              =   $data->email;
            $kirim['direktorat']         =   $data->divisis->id??11111111;
            $kirim['divisi']             =   $data->divisis->id??11111111;
            $kirim['last_login_at']      =   $data->last_login_at;
            $kirim['role']               =   $data->role;
            $kirim['login_at']           =   Carbon::now();
            $kirim['token_bri']          =   $access_token;
            $kirim['token']              =   $data->createToken('web-token')->plainTextToken;

            Log::info("DATA BE LOGIN", [$kirim]);
            return response()->json([
                "status"    =>  1,
                "data"      =>  $kirim
            ],200);
*/
            //sukses maka :
           $ResponseCostcenter     = '';
            $Responsenama = '';
            $hitLDAP = true;
            if ($hitLDAP !== false) {
                $data           = User::where('personal_number', $request->personal_number)->first();
                if (isset($data->personal_number)){
//                    // ==================================
//                    // get token BRI
//                    // ==================================
//                        // init hasil
                        $access_token = '';
//                        // ---------------
                        $ch = curl_init();
                        $headers  = [
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                                ];
                        $postData = [
                            'client_id'     => 'briknow',
                            'client_secret' => 'fa14f443b2433ecc7a3091942aff8c41fdc92a90',
                            'scope'         => 'public',
                            'grant_type'    => 'client_credentials',
                        ];
                        curl_setopt($ch, CURLOPT_URL,config('app.api_auth_bristar'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result     = curl_exec ($ch);
                        $hasil      = json_decode($result);
                        if (isset($hasil->access_token)) {
                            $access_token = $hasil->access_token;
                        }else{
                            $msg['message'] =   'Get User Failed';
                            return response()->json([
                                "status"    =>  0,
                                "data"      =>  $msg
                            ],200);
                        }
//                    // ==================================
                    // save log
                    $datauser['ip_address']     =   request()->ip;
                    try {
                        $position = \Location::get($datauser['ip_address']);
                    } catch (\Throwable $th) {}
                    $cekleveling    = Level::where('xp', '<=',$data->xp)->orderby('xp','desc')->first();
                    $datauser['user_id']          =   $data->id;
                    $datauser['country_name']     =   $position->countryName??null;
                    $datauser['country_code']     =   $position->countryName??null;
                    $datauser['region_code']      =   $position->regionCode??null;
                    $datauser['city_name']        =   $position->cityName??null;
                    $datauser['zip_code']         =   $position->zipCode??null;
                    $datauser['latitude']         =   $position->latitude??null;
                    $datauser['longitude']        =   $position->longitude??null;
                    $datauser['area_code']        =   $position->areaCode??null;
                    User_log::create($datauser);
//
//                    // Event Activity
                    event(new TriggerActivityEvent(6, $request->personal_number));
//
                    // return
                    $data->tokens()->delete();
                    $kirim['id']                 =   $data->id;
                    $kirim['avatar_id']          =   $data->avatar_id;
                    $kirim['exp']                =   $data->xp;
                    $kirim['level_id']           =   $cekleveling->id;
                    $kirim['level_name']         =   $cekleveling->name;
                    $kirim['name']               =   $data->name;
                    $kirim['personal_number']    =   $data->personal_number;
                    $kirim['username']           =   $data->username;
                    $kirim['email']              =   $data->email;
                    $kirim['direktorat']         =   $data->divisis->id??11111111;
                    $kirim['divisi']             =   $data->divisis->id??11111111;
                    $kirim['last_login_at']      =   $data->last_login_at;
                    $kirim['role']               =   $data->role;
                    $kirim['login_at']           =   Carbon::now();
                    $kirim['token_bri']          =   $access_token??'dummy';
                    $kirim['token']              =   $data->createToken('web-token')->plainTextToken;
//
                    return response()->json([
                        "status"    =>  1,
                        "data"      =>  $kirim
                    ],200);
                }else{
//                    // -----------------------------
//                    // tempat API Auth
//                    // -----------------------------
//                        // init hasil
                        $access_token = '';
//                        // ---------------
                        $ch = curl_init();
                        $headers  = [
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                                ];
                        $postData = [
                            'client_id'     => 'briknow',
                            'client_secret' => 'fa14f443b2433ecc7a3091942aff8c41fdc92a90',
                            'scope'         => 'public',
                            'grant_type'    => 'client_credentials',
                        ];
                        curl_setopt($ch, CURLOPT_URL,config('app.api_auth_bristar'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result     = curl_exec ($ch);
                        $hasil      = json_decode($result);
                        if (isset($hasil->access_token)) {
                            $access_token = $hasil->access_token;
//
//                            // -----------------------------
//                            // tempat API Detail User
//                            // -----------------------------
                            $ch2 = curl_init();
                            $headers  = [
                                        'Content-Type: application/json',
                                        'Accept: application/json',
                                        "Authorization: Bearer $access_token",
                                    ];
                            $postData = [
                                'pernr'     => $request->personal_number,
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
                                $ResponseCostcenter = $hasil2->responseData->COST_CENTER;
                                $Responsenama       = $hasil2->responseData->NAMA;
                            }else{
                                $msg['message'] =   'Akun Tidak Ditemukan';
                                return response()->json([
                                    "status"    =>  0,
                                    "data"      =>  $msg
                                ],200);
                            }
                        }else{
                            $msg['message'] =   'Get User Failed';
                            return response()->json([
                                "status"    =>  0,
                                "data"      =>  $msg
                            ],200);
                        }
//                    // -----------------------------
//
//                    //kita perlu hasil kembalian API Bristar disini untuk dilakukan REGISTRASI AKUN yg belum Terdaftar tapi Personal Number valid
//                    //daftarkan jika kembalian LDAP tidak menemukan
                    $dataDivisi = Divisi::where('cost_center', $ResponseCostcenter)->first();
//                    // add user
                    $new                    = new User();
                    $new->name              = $Responsenama??'User';
                    $new->personal_number   = $request->personal_number;
                    $new->username          = $Responsenama??'User';
                    $new->email             = $hitLDAP;
                    $new->role              = 0;
                    $new->divisi            = isset($dataDivisi->id)? $dataDivisi->id : 11111111;
                    $new->last_login_at     = Carbon::now();
                    $new->xp                = 0;
                    $new->avatar_id         = 1;
                    $new->save();
//
//                    //setelah save loginkan otomatis tanpa password
                    $cek_user_baru = User::where('personal_number',$request->personal_number)->first();
                    $datauser['ip_address']       =   request()->ip;
                    try {
                        $position = \Location::get($datauser['ip_address']);
                    } catch (\Throwable $th) {}
                    $cekleveling    = Level::where('xp', '<=',$cek_user_baru->xp)->orderby('xp','desc')->first();
                    $datauser['user_id']          =   $cek_user_baru->id;
                    $datauser['country_name']     =   $position->countryName??null;
                    $datauser['country_code']     =   $position->countryName??null;
                    $datauser['region_code']      =   $position->regionCode??null;
                    $datauser['city_name']        =   $position->cityName??null;
                    $datauser['zip_code']         =   $position->zipCode??null;
                    $datauser['latitude']         =   $position->latitude??null;
                    $datauser['longitude']        =   $position->longitude??null;
                    $datauser['area_code']        =   $position->areaCode??null;
                    User_log::create($datauser);
//
//                    // Event Activity
                    event(new TriggerActivityEvent(6, $cek_user_baru->personal_number));
//
//                    // return
                    $cek_user_baru->tokens()->delete();
                    $kirim['id']                 =   $cek_user_baru->id;
                    $kirim['avatar_id']          =   $cek_user_baru->avatar_id;
                    $kirim['exp']                =   $cek_user_baru->xp;
                    $kirim['level_id']           =   $cekleveling->id;
                    $kirim['level_name']         =   $cekleveling->name;
                    $kirim['name']               =   $cek_user_baru->name;
                    $kirim['personal_number']    =   $cek_user_baru->personal_number;
                    $kirim['username']           =   $cek_user_baru->username;
                    $kirim['email']              =   $cek_user_baru->email;
                    // $kirim['is_admin']           =   $cek_user_baru->is_admin;
                    $kirim['direktorat']         =   $cek_user_baru->divisis->id??11111111;
                    $kirim['divisi']             =   $cek_user_baru->divisis->id??11111111;
                    $kirim['last_login_at']      =   $cek_user_baru->last_login_at;
                    $kirim['role']               =   $cek_user_baru->role;
                    $kirim['login_at']           =   Carbon::now();
                    $kirim['token_bri']          =   $access_token;
                    $kirim['token']              =   $cek_user_baru->createToken('web-token')->plainTextToken;

                    return response()->json([
                        "status"    =>  1,
                        "data"      =>  $kirim
                    ],200);
                }
            } else{
                $data['message']    =   'Personal Number / Password Salah';
                return response()->json([
                    'status'    =>  0,
                    'data'      =>  $data
                ],200);
            }

    }
    // handle tangkepan dari bristar
    public function proses_api(){
        $tmp = request()->all();

        //DEBUGGING CEK KIRIMAN FE UDAH OK BLM
        // return response()->json([
        //     "status"    =>  1,
        //     "key"      =>  $tmp['key'],
        //     "user"      =>  $tmp['user'],
        //     "app_id"      =>  $tmp['app_id'],
        // ],200);

        //proses HIT ke HTSPACE buat GET INFO
        try {
            $ch = curl_init();
            $headers  = [
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: application/json',
                    ];
            $postData = [
                'key'    => $tmp['key'],
                'user'   => $tmp['user'],
                'app_id' => config('app.api_app_id'),
            ];
            // return $postData;
            curl_setopt($ch, CURLOPT_URL,config('app.api_htspace'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result     = curl_exec ($ch);
            $hasil = json_decode($result);
	          //return response()->json(['tujuan' => config('app.api_htspace'), 'data' => $postData,'response_hcspace' => $result]);
            if (isset($hasil->status)) {
                if ($hasil->status == "Success") {
                    // ==================================
                    // get token BRI
                    // ==================================
                        // init hasil
                        $access_token = '';
                        // // ---------------
                        $ch = curl_init();
                        $headers  = [
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                                ];
                        $postData = [
                            'client_id'     => 'briknow',
                            'client_secret' => 'fa14f443b2433ecc7a3091942aff8c41fdc92a90',
                            'scope'         => 'public',
                            'grant_type'    => 'client_credentials',
                        ];
                        curl_setopt($ch, CURLOPT_URL,config('app.api_auth_bristar'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result     = curl_exec ($ch);
                        $hasil_auth_bristar   = json_decode($result);
			                  //return response()->json(['status' => $hasil]);
                        if (isset($hasil_auth_bristar->access_token)) {
                            $access_token = $hasil_auth_bristar->access_token;
                        }else{
                            $msg['message'] =   'Get User Failed';
                            return response()->json([
                                "status"    =>  0,
                                "data"      =>  $msg
                            ],200);
                        }
                    // ==================================

                    // JIKA SUKSES MAKA -> result api di create ke db
                    $cek_user = User::where('personal_number',$hasil->profile->pernr)->first();
		                //return response()->json(['status' => $cek_user]);
                    if (isset($cek_user->personal_number)){
                        //loginkan tanpa password karna via bristar
                        // $data = User::where('personal_number', $hasil->profile->pernr)->first();
                        // save log
                        $datauser['ip_address']       =   $tmp['ip'];
                        try {
                            $position = \Location::get($datauser['ip_address']);
                        } catch (\Throwable $th) {}
                        $cekleveling    = Level::where('xp', '<=',$cek_user->xp)->orderby('xp','desc')->first();
                        $datauser['user_id']          =   $cek_user->id;
                        $datauser['country_name']     =   $position->countryName??null;
                        $datauser['country_code']     =   $position->countryName??null;
                        $datauser['region_code']      =   $position->regionCode??null;
                        $datauser['city_name']        =   $position->cityName??null;
                        $datauser['zip_code']         =   $position->zipCode??null;
                        $datauser['latitude']         =   $position->latitude??null;
                        $datauser['longitude']        =   $position->longitude??null;
                        $datauser['area_code']        =   $position->areaCode??null;
                        User_log::create($datauser);

                        // Event Activity
                        event(new TriggerActivityEvent(6, $hasil->profile->pernr));

                        // hapus toke user terkait yang udah existing
                        $cek_user->tokens()->delete();

                        // return
                        $kirim['id']                 =   $cek_user->id;
                        $kirim['avatar_id']          =   $cek_user->avatar_id;
                        $kirim['exp']                =   $cek_user->xp;
                        $kirim['level_id']           =   $cekleveling->id;
                        $kirim['level_name']         =   $cekleveling->name;
                        $kirim['name']               =   $cek_user->name;
                        $kirim['personal_number']    =   $cek_user->personal_number;
                        $kirim['username']           =   $cek_user->username;
                        $kirim['email']              =   $cek_user->email;
                        $kirim['direktorat']         =   $cek_user->divisis->id??11111111;
                        $kirim['divisi']             =   $cek_user->divisis->id??11111111;
                        $kirim['last_login_at']      =   $cek_user->last_login_at;
                        $kirim['role']               =   $cek_user->role;
                        $kirim['login_at']           =   Carbon::now();
                        $kirim['token_bri']          =   $access_token;
                        $kirim['token']              =   $cek_user->createToken('web-token')->plainTextToken;

                        return response()->json([
                            "status"    =>  1,
                            "data"      =>  $kirim
                        ],200);
                    } else {
                        // get divisi
                        $dataDivisi = Divisi::where('cost_center', $hasil->profile->cost_center)->first();
                        //daftarkan
                        $new = new User();
                        $new->name = $hasil->profile->nama;
                        $new->personal_number = $hasil->profile->pernr;
                        $new->username = $hasil->profile->nama;
                        $new->role     = 0;
                        $new->divisi =  isset($dataDivisi->id)? $dataDivisi->id : 11111111;
                        $new->last_login_at =  Carbon::now();
                        $new->xp = 0;
                        $new->avatar_id = 1;
                        $new->save();

                        //setelah save loginkan otomatis tanpa password
                        $cek_user_baru = User::where('personal_number',$hasil->profile->pernr)->first();
                        $datauser['ip_address']       =   $tmp['ip'];
                        try {
                            $position = \Location::get($datauser['ip_address']);
                        } catch (\Throwable $th) {}
                        $cekleveling    = Level::where('xp', '<=',$cek_user_baru->xp)->orderby('xp','desc')->first();
                        $datauser['user_id']          =   $cek_user_baru->id;
                        $datauser['country_name']     =   $position->countryName??null;
                        $datauser['country_code']     =   $position->countryName??null;
                        $datauser['region_code']      =   $position->regionCode??null;
                        $datauser['city_name']        =   $position->cityName??null;
                        $datauser['zip_code']         =   $position->zipCode??null;
                        $datauser['latitude']         =   $position->latitude??null;
                        $datauser['longitude']        =   $position->longitude??null;
                        $datauser['area_code']        =   $position->areaCode??null;
                        User_log::create($datauser);

                        // Event Activity
                        event(new TriggerActivityEvent(6, $cek_user_baru->personal_number));

                        // return
                        $cek_user_baru->tokens()->delete();
                        $kirim['id']                 =   $cek_user_baru->id;
                        $kirim['avatar_id']          =   $cek_user_baru->avatar_id;
                        $kirim['exp']                =   $cek_user_baru->xp;
                        $kirim['level_id']           =   $cekleveling->id;
                        $kirim['level_name']         =   $cekleveling->name;
                        $kirim['name']               =   $cek_user_baru->name;
                        $kirim['personal_number']    =   $cek_user_baru->personal_number;
                        $kirim['username']           =   $cek_user_baru->username;
                        $kirim['email']              =   $cek_user_baru->email;
                        $kirim['direktorat']         =   $cek_user_baru->divisis->id??11111111;
                        $kirim['divisi']             =   $cek_user_baru->divisis->id??11111111;
                        $kirim['last_login_at']      =   $cek_user_baru->last_login_at;
                        $kirim['role']               =   $cek_user_baru->role;
                        $kirim['login_at']           =   Carbon::now();
                        $kirim['token_bri']          =   $access_token;
                        $kirim['token']              =   $cek_user_baru->createToken('web-token')->plainTextToken;

                        return response()->json([
                            "status"    =>  1,
                            "data"      =>  $kirim
                        ],200);
                    }
                }else{
                    //hasil status ada , TAPI Bukan Success
                    $kirim2['message']  =   $hasil->message;
                    return response()->json([
                        "status"    =>  0,
                        "data"      =>  $kirim2
                    ],200);
                }
            }else{
                //hasil status tidak ada dari BRISTAR
                $kirim3['message']  =   "Problem Connection To Bristars";
                return response()->json([
                    "status"    =>  0,
                    "data"      =>  $kirim3
                ],200);
            }
        }catch (\Throwable $th) {
            // masalah ketika HIT api BRISTAR
           $kirim4['message']  =   "Something Problem";
           return response()->json([
               "status"    =>  0,
               "data"      =>  $kirim4
           ],200);
        }
    }

    public function LDAP($pn, $pwd){
        $opts_ldap = array(
            'http' => array(
                'user_agent'=>'PHPSoapClient',
                )
        );

        // $opts_ldap = array(
        //     'ssl' => array(
        //         'ciphers'=>'RC4-SHA',
        //         'verify_peer'=>false,
        //         'verify_peer_name'=>false
        //         )
        // );

        $params = array (
            'cache_wsdl'   =>  WSDL_CACHE_NONE,
            'stream_context' => stream_context_create($opts_ldap)
        );

        // $params = array (
        //     'encoding' => 'UTF-8',
        //     'verifypeer' => false,
        //     'verifyhost' => false,
        //     'soap_version' => SOAP_1_2,
        //     'trace' => 1,
        //     'exceptions' => 1,
        //     "connection_timeout" => 180,
        //     'stream_context' => stream_context_create($opts_ldap)
        // );

        $url = "https://wsuser.bri.co.id/beranda/ldap/ws/ws_adUser.php?wsdl";

        try{
            $client = new SoapClient($url,$params);
            $response_ldap = $client->validate_aduser($pn,$pwd);

            if ($response_ldap <> '') {
                return $response_ldap;
            }else{
                return false;
            }
        }catch(SoapFault $fault) {
            return false;
        }
    }
}
