<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $result = $this->authService->register($data);

            return response()->json($result, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro inesperado. Tente novamente.'], 500);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        try {
            $result = $this->authService->verifyOtp($data['email'], $data['code']);

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro inesperado. Tente novamente.'], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->login($data);

            return response()->json($result);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro inesperado. Tente novamente.'], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json(['message' => 'Logout realizado com sucesso.']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro inesperado. Tente novamente.'], 500);
        }
    }

    public function canAccess(): JsonResponse
    {
        return response()->json(['authenticated' => true]);
    }
}
