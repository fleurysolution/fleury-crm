<?php

namespace App\Libraries;

class ApprovalStatus
{
    // Request statuses
    public const REQUEST_PENDING   = 'pending';
    public const REQUEST_APPROVED  = 'approved';
    public const REQUEST_REJECTED  = 'rejected';
    public const REQUEST_CANCELLED = 'cancelled';

    // Step statuses
    public const STEP_PENDING  = 'pending';
    public const STEP_APPROVED = 'approved';
    public const STEP_REJECTED = 'rejected';
    public const STEP_SKIPPED  = 'skipped';

    public static function canTransitionRequest(string $from, string $to): bool
    {
        $map = [
            self::REQUEST_PENDING   => [self::REQUEST_APPROVED, self::REQUEST_REJECTED, self::REQUEST_CANCELLED],
            self::REQUEST_APPROVED  => [],
            self::REQUEST_REJECTED  => [],
            self::REQUEST_CANCELLED => [],
        ];

        return in_array($to, $map[$from] ?? [], true);
    }

    public static function canTransitionStep(string $from, string $to): bool
    {
        $map = [
            self::STEP_PENDING  => [self::STEP_APPROVED, self::STEP_REJECTED, self::STEP_SKIPPED],
            self::STEP_APPROVED => [],
            self::STEP_REJECTED => [],
            self::STEP_SKIPPED  => [],
        ];

        return in_array($to, $map[$from] ?? [], true);
    }
}
