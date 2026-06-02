<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getAll();

            return response()->json(['users' => $users]);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro ao listar usuários.'], 500);
        }
    }
}
