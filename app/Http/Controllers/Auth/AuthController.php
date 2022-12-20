<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\User_log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view('Auth.login');
    }

    public function long_many_time_login(){
        if (session::has('l_e_num')) {
            $count = session::pull('l_e_num');
            $count++;
            if ($count < 3) {
                session::put('l_e_num',$count);
            }else{
                // clear
                session::flush();
                // 1 menit suspense
                $time = Carbon::now()->addMinutes(1);
                session::put('l_e_suspend',$time);
                return true;
            }
        }else{
            session::put('l_e_num',1);
        }
        return false;
    }

    public function login_proses(Request $request)
    {
        $request->validate([
            'personal_number'       =>  'required|numeric|digits:8',
            'katasandi'             =>  'required',
            'captcha'               =>  'required|captcha'
        ]);
        $data = User::where('personal_number', $request->personal_number)->first();
        if (!empty($data)){
            if(Hash::check($request->katasandi, $data->password)){
                // refresh session
                Session::flush();

                $temp['id'] = $data->id;
                $temp['name'] = $data->name;
                $temp['personal_number'] = $data->personal_number;
                $temp['username'] = $data->username;
                $temp['email'] = $data->email;
                $temp['is_admin'] = $data->is_admin;
                $temp['direktorat'] = $data->direktorat;
                $temp['divisi'] = $data->divisi;
                $temp['last_login_at'] = $data->last_login_at;
                $temp['role'] = $data->role;
                $temp['login_at'] = Carbon::now();
                session($temp);

                session(['logged_in' => true]);

                // save log
                $datauser['ip_address']     =   $_SERVER['REMOTE_ADDR'];   
                try {
                    $position = \Location::get($_SERVER['REMOTE_ADDR']);
                } catch (\Throwable $th) {}
                $datauser['user_id']          =   session('id');   
                $datauser['country_name']     =   $position->countryName??null;   
                $datauser['country_code']     =   $position->countryName??null;   
                $datauser['region_code']      =   $position->regionCode??null;   
                $datauser['city_name']        =   $position->cityName??null;   
                $datauser['zip_code']         =   $position->zipCode??null;   
                $datauser['latitude']         =   $position->latitude??null;   
                $datauser['longitude']        =   $position->longitude??null;   
                $datauser['area_code']        =   $position->areaCode??null;   
                User_log::create($datauser);

                return redirect('/');
            } else {
                // many time to login ??
                if ($this->long_many_time_login() == true) {
                    Session::flash('error', 'Terlalu Sering Melakukan Kesalahan Saat Login'); 
                }else{
                    Session::flash('error', 'Personal Number atau Password Salah'); 
                }

                return back();
            }
        } else {
            // many time to login ??
            if ($this->long_many_time_login() == true) {
                Session::flash('error', 'Terlalu Sering Melakukan Kesalahan Saat Login'); 
            }else{
                Session::flash('error', 'Personal Number atau Password Salah'); 
            }

            Session::flash('error', 'Akun Tidak Terdaftar'); 
            return back();
        }
    }

    public function logout(Request $request)
    {
        Cache::put('user-is-online-'.session('id'), true, 0);
        $request->session()->flush();
        return redirect('/login');
    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img('flat')]);
    }
}
