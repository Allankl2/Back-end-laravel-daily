<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    private const MAX_ATTEMPTS = 4;
    private const EXPIRES_MINUTES = 10;

    public function __construct(
        private OtpRepositoryInterface $otpRepository
    ) {}

    public function generate(string $email): void
    {
        $this->otpRepository->deleteByEmail($email);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->otpRepository->create($email, $code, now()->addMinutes(self::EXPIRES_MINUTES));

        Mail::to($email)->send(new OtpMail($code));

        Log::info('OTP gerado e enviado.', ['email' => $email]);
    }

    public function verify(string $email, string $code): void
    {
        $otp = $this->otpRepository->findValidByEmail($email);

        if (! $otp) {
            throw new \RuntimeException('OTP não encontrado ou expirado. Faça o registro novamente.');
        }

        if ($otp->blocked_at !== null) {
            throw new \RuntimeException('OTP bloqueado por excesso de tentativas. Faça o registro novamente.');
        }

        if ($otp->code !== $code) {
            $this->otpRepository->incrementAttempts($otp);

            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $this->otpRepository->block($otp);
                Log::warning('OTP bloqueado após tentativas excedidas.', ['email' => $email]);
                throw new \RuntimeException('Número máximo de tentativas atingido. Faça o registro novamente.');
            }

            $remaining = self::MAX_ATTEMPTS - $otp->attempts;
            throw new \RuntimeException("Código inválido. Você tem {$remaining} tentativa(s) restante(s).");
        }

        $this->otpRepository->delete($otp);
    }
}
