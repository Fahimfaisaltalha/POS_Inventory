<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\JwtToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordSentOtpRequest;
use App\Http\Requests\Auth\ResetPasswordVerifyOtpRequest;
use App\Mail\SendOtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function sendOtp(ResetPasswordSentOtpRequest $request)
    {
        try {
            $otp = mt_rand(100000, 999999);
            Otp::create([
                'email' => $request->email,
                'otp' => $otp,
            ]);

            Mail::to($request->email)->send(new SendOtpMail($otp));

            return response()->json([
                'status' => true,
                'message' => 'Otp Sent to Your Email',
            ]);
        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' '  . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function verifyOtp(ResetPasswordVerifyOtpRequest $request)
    {
        try {
            Otp::where('email', $request->email)->where('otp', $request->otp)->update([
                'status' => true,
            ]);

            $exp = time() + 3600; // 1 hour from now
            $token = JwtToken::createToken(['email' => $request->email], $exp);

            if ($token['error']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create token',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Otp Verified',
            ], 200)->cookie('resetPasswordToken', $token['token'], 60); // 60 minutes

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' '  . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            if (!$request->cookie('resetPasswordToken')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid password request attempt',
                ], 401);
            }

            $decode = JwtToken::verifyToken($request->cookie('resetPasswordToken'));
            if ($decode['error']) {
                return response()->json([
                    'status' => false,
                    'message' => $decode['message'],
                ], 401);
            }

            // Fixed: Use 'data' instead of 'payload' and hash the password
            $user = User::where('email', $decode['data']->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            // Clear used OTP
            Otp::where('email', $decode['data']->email)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Password has been reset successfully',
            ], 200)->cookie('resetPasswordToken', '', -1); // Clear cookie

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' '  . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
