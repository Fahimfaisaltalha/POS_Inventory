<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(){
        return response()->json([
            'status'=>'success',
            'message'=>'User Logout',
        ])->withCookie(cookie('token', null, -1));
    }
}
