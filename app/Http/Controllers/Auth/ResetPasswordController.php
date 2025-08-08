<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\JwtToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordSentOtpRequest;
use App\Http\Requests\Auth\ResetPasswordVerifyOtpRequest;
use App\Mail\SendOtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function sendOtp(ResetPasswordSentOtpRequest $request){
        try{
        $otp =mt_rand(100000, 999999);
        Otp::create([
            'email' => $request->email,
            'otp' => $otp
        ]);

        Mail::to($request->email) ->send(new SendOtpMail($otp));
        return response()->json([
            'success' => true,
            'message' => 'Otp sent to your Email'
        ], 200);
        }catch(\Exception $e){
            Log::critical("message: ".$e->getMessage().''. $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP'
            ], 500);
        }
    }

     public function verifyOtp(ResetPasswordVerifyOtpRequest $request){
        try{
             Otp::where('email',$request->email)->where('otp',$request->otp)->update([
                'status'=>true,
            ]);
            $exp= +3600;
            $token= JwtToken::createToken(['email'=>$request->email], $exp);
            return response()->json([
                'success'=>true,
                'message'=> 'Otp Verified',
            ],200)->cookie('resetPasswordToken',$token,$exp);

        return response()->json([
            'success' => true,
            'message' => 'Otp sent to your Email'
        ], 200);
        }catch(\Exception $e){
            Log::critical("message: ".$e->getMessage().''. $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP'
            ], 500);
        }
    }
}
