<?php

namespace App\Repositories;

use App\Models\OtpCode;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Carbon\Carbon;

class OtpRepository implements OtpRepositoryInterface
{
    public function deleteByEmail(string $email): void
    {
        OtpCode::where('email', $email)->delete();
    }

    public function create(string $email, string $code, Carbon $expiresAt): OtpCode
    {
        return OtpCode::create([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidByEmail(string $email): ?OtpCode
    {
        return OtpCode::where('email', $email)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    public function incrementAttempts(OtpCode $otp): void
    {
        $otp->increment('attempts');
    }

    public function block(OtpCode $otp): void
    {
        $otp->update(['blocked_at' => now()]);
    }

    public function delete(OtpCode $otp): void
    {
        $otp->delete();
    }
}
