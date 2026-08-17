<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code_hash',
        'purpose',
        'attempts',
        'expires_at',
        'verified_at',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new numeric OTP, persist its hash, and return the RAW code
     * (the raw code is only ever available at creation time — it is never stored).
     */
    public static function generateFor(User $user, string $purpose = 'registration', ?string $ip = null): array
    {
        $length = (int) config('otp.length', 6);
        $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);

        $otp = self::create([
            'user_id' => $user->id,
            'code_hash' => self::hashCode($code),
            'purpose' => $purpose,
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes((int) config('otp.expires_in_minutes', 10)),
            'ip_address' => $ip,
        ]);

        return [$otp, $code];
    }

    public static function hashCode(string $code): string
    {
        return hash('sha256', $code);
    }

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }

    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= (int) config('otp.max_attempts', 5);
    }

    public function matches(string $rawCode): bool
    {
        return hash_equals($this->code_hash, self::hashCode($rawCode));
    }
}
