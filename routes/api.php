<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Middleware\JwtTokenVerify;
use Illuminate\Support\Facades\Route;

Route::post('register',[RegisterController::class,'register']);
Route::post('password/reset/send/otp',[ResetPasswordController::class,'sendOtp']);
Route::post('password/reset/verify/otp',[ResetPasswordController::class,'verifyOtp']);
Route::post('password/reset/',[ResetPasswordController::class,'resetPassword']);
Route::post('login',[LoginController::class,'login']);

Route::get('profile',[ProfileController::class,'profile'])->middleware(JwtTokenVerify::class);
Route::post('logout',[LogoutController::class,'logout'])->middleware(JwtTokenVerify::class);
