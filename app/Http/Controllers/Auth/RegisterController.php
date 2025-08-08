<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try{
            User::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully'
            ], 201);


        }catch(\Exception $e){
            Log::critical("message: ".$e->getMessage().''. $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success'=>false,
                'message' => 'Something Went wrong'
            ],500);
        }

    }
}
