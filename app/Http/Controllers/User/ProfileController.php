<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    public function profile()
    {

            $data = Auth::user();

           return  new \App\Http\Resources\UserResource($data);
}
}
