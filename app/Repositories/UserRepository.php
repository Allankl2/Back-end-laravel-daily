<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data)->save();

        return $user;
    }

    public function markEmailVerified(User $user): User
    {
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findAll(): Collection
    {
        return User::all();
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function createToken(User $user, string $name = 'auth_token'): string
    {
        return $user->createToken($name, ['*'], now()->addDay())->plainTextToken;
    }
}
