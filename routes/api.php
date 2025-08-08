<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('register',[RegisterController::class,'register']);
Route::post('password/reset/send/otp',[ResetPasswordController::class,'sendOtp']);
Route::post('password/reset/verify/otp',[ResetPasswordController::class,'verifyOtp']);
