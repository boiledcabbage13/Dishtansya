<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

use App\Services\Auth\Register;
use App\Services\Auth\Login;

use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request, Login $action) {
        try {
            $result = $action->execute($request->email, $request->password);

            return response()->json([
                'data' => new UserResource($result),
                'message' => 'User successfully logged in.'
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'data' => null,
                'message' => $th->getMessage()
            ], 401);
        }
    }

    public function register(RegisterRequest $request, Register $action) {
        $data = $request->only(['email', 'password']);

        $result = $action->execute($data);

        return response()->json([
            'data' => new UserResource($result),
            'message' => 'User successfully registered.'
        ], 201);
    }
}
