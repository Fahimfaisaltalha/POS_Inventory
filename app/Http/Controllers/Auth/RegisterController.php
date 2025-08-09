<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Arr;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validator = $request->validated();
        $userData = Arr::only($validator, ['name', 'email', 'password']);
        $profileData = Arr::only($validator, ['phone', 'address']);


        try{
            $user=User::create($userData);
            $profileData['user_id']=$user->id;

            if($request->hasFile('image')){
                $Path = $request->file('image')->store('avatars', 'public');
                $profileData['avatar'] = $Path;
            }

            Profile::create($profileData);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' =>$user
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
