<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\JwtToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Credentials',
                ], 401);
            }

            $userData = [
                'email' => $user->email,
                'id' => $user->id
            ];

            $exp = time() + (3600 * 24); // 24 hours from now
            $token = JwtToken::createToken($userData, $exp);

            if ($token['error']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create token',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Login Success',
            ], 200)->cookie('token', $token['token'], 1440); // 1440 minutes = 24 hours

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' '  . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
