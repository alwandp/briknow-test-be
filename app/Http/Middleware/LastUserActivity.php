<?php

namespace App\Http\Middleware;

use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(session('logged_in')){
            // perbarui keterangan
            $data['last_login_at'] = Carbon::now();
            User::where('id',session('id'))->update($data);

            $expired_at =   Carbon::now()->addMinutes(1);   
            Cache::put('user-is-online-'.session('id'),true,$expired_at);
        }
        return $next($request);
    }
}
