<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials, ['exp' => 1440])) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }

            $tokenExpiration = now()->addDay(1); // Token expira en 1 día

            $payload = JWTFactory::sub(Auth::user()->id)
                ->aud('api')
                ->expiresAt($tokenExpiration)
                ->make();

            $token = JWTAuth::encode($payload)->get();
            
            //$tokenResponse = compact('token');
            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data'=>[
                    'token' => $token,
                    'minutes_to_expire' => 1440,
                ]
            ]
            );
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }

        
    }
}