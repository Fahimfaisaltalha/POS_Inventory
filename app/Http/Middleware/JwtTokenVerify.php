<?php

namespace App\Http\Middleware;

use App\Helpers\JwtToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JwtTokenVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$request->cookie('token')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $decode = JwtToken::verifyToken($request->cookie('token'));
            if ($decode['error']) {
                return response()->json([
                    'status' => false,
                    'message' => $decode['message'],
                ], 401);
            }

            // Fixed: Use 'data' instead of 'payload' to match JwtToken helper
            $data = $decode['data'];
            $user = User::where('id', $data->id)
                ->where('email', $data->email)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 401);
            }

            Auth::setUser($user);

            return $next($request);
        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
