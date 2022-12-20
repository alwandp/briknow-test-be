<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {
            $request->user()->tokens()->where('id',request()->user()->currentAccessToken()->id)->delete();

            $data['message']    =   "Anda Berhasil Logout";
            return response()->json([
                "status"    => '1',
                "data"      => $data
            ]);
        } catch (\Throwable $th) {
            $data['message']    =   "Anda Gagal Logout";
            return response()->json([
                "status"    => '0',
                "data"      => $data
            ]);
        }
    }
}
