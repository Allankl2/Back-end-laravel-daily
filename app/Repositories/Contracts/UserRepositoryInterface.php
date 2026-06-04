<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function markEmailVerified(User $user): User;
    public function findByEmail(string $email): ?User;
    public function findAll(): Collection;
    public function revokeAllTokens(User $user): void;
    public function revokeCurrentToken(User $user): void;
    public function createToken(User $user, string $name = 'auth_token'): string;
}
