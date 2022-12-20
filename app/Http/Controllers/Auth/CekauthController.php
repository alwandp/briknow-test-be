<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekauthController extends Controller
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
            return response()->json([
                'status'    =>  '1',
                'data'      =>  $request->user()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'    =>  '0',
                'data'      =>  "Unauthenticated"
            ]);
        }
    }
}
