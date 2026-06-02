<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function getAll(): Collection
    {
        $users = $this->userRepository->findAll();

        Log::info('Listagem de usuários acessada.', ['total' => $users->count()]);

        return $users;
    }
}
