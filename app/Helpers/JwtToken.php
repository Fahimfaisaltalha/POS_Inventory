<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Js;

class JwtToken
{
    public static function createToken(array $userData, int $exp):JsonResponse
    {
        try{
        $key=config('jwt.jwt_key');
        $payload=$userData+[
            'iss'=>'PosInventoryApp',
            'iat'=>time(),
            'exp'=>$exp
        ];
        $token= JWT::encode($payload, $key, 'HS256');
        return response()->json([
            'error' => false,
            'token' => $token

        ]);
        }catch(\Exception $e){
            Log::critical($e->getMessage().''. $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'error'=> true,
                'message'=> 'Failed to create JWT token'
            ]);
        }
    }

    public static function verifyToken(string $token):JsonResponse
    {
        try{
            $key = config('jwt.jwt_key');
            if(!$token){
                return response()->json([
                    'error' => true,
                    'payload'=>[],
                    'message' => 'Token is required'
                ]);
            }
            $payload= JWT::decode($token, new Key(($key),'HS256'));
            return response()->json([
                'error' => false,
                'data' => $payload,
                'message'=>'Data found Successfully'
            ]);
        }catch(\Exception $e){
            Log::critical($e->getMessage().''. $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'error'=> true,
                'payload'=>[],
                'message'=> 'Failed to verify JWT token'
            ]);
        }
    }

}
