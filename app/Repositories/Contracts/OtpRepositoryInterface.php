<?php

namespace App\Repositories\Contracts;

use App\Models\OtpCode;
use Carbon\Carbon;

interface OtpRepositoryInterface
{
    public function deleteByEmail(string $email): void;
    public function create(string $email, string $code, Carbon $expiresAt): OtpCode;
    public function findValidByEmail(string $email): ?OtpCode;
    public function incrementAttempts(OtpCode $otp): void;
    public function block(OtpCode $otp): void;
    public function delete(OtpCode $otp): void;
}
