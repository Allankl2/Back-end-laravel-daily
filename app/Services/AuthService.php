<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private OtpService $otpService
    ) {}

    public function register(array $data): array
    {
        try {
            $user = $this->userRepository->findByEmail($data['email']);

            if ($user) {
                if ($user->email_verified_at !== null) {
                    throw new \RuntimeException('Este email já está cadastrado.');
                }

                $user->name     = $data['name'];
                $user->password = $data['password'];
                $user->save();
            } else {
                $user = $this->userRepository->create($data);
            }

            $this->otpService->generate($data['email']);

            Log::info('OTP de registro enviado.', ['email' => $data['email']]);

            return ['message' => 'Código OTP enviado para seu email. Verifique sua caixa de entrada.'];
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (QueryException $e) {
            Log::error('Erro ao criar usuário no banco.', ['error' => $e->getMessage()]);

            throw new \RuntimeException('Erro ao criar usuário. Tente novamente.');
        }
    }

    public function verifyOtp(string $email, string $code): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user) {
            throw new \RuntimeException('Usuário não encontrado.');
        }

        $this->otpService->verify($email, $code);

        $user->email_verified_at = now();
        $user->save();

        $user->tokens()->delete();
        $token = $user->createToken('auth_token', ['*'], now()->addDay())->plainTextToken;

        Log::info('Usuário verificado via OTP.', ['user_id' => $user->id, 'email' => $user->email]);

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            Log::warning('Tentativa de login com credenciais inválidas.', ['email' => $data['email']]);

            throw new AuthorizationException('Credenciais inválidas.');
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token', ['*'], now()->addDay())->plainTextToken;

        Log::info('Usuário autenticado.', ['user_id' => $user->id, 'email' => $user->email]);

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if (! $token) {
            Log::warning('Tentativa de logout sem token válido.', ['user_id' => $user->id]);

            throw new \RuntimeException('Token não encontrado.');
        }

        $token->delete();

        Log::info('Usuário deslogado.', ['user_id' => $user->id]);
    }
}
