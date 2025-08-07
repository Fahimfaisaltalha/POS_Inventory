<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class JwtToken
{
    public static function createToken(array $userData, int $exp){
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

}
