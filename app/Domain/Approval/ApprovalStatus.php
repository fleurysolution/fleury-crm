<?php

namespace App\Domain\Approval;

final class ApprovalStatus
{
    // Request-level statuses
    public const REQUEST_PENDING   = 'pending';
    public const REQUEST_APPROVED  = 'approved';
    public const REQUEST_REJECTED  = 'rejected';
    public const REQUEST_CANCELLED = 'cancelled';

    // Step-level statuses
    public const STEP_PENDING  = 'pending';
    public const STEP_APPROVED = 'approved';
    public const STEP_REJECTED = 'rejected';
    public const STEP_SKIPPED  = 'skipped';

    public static function requestStatuses(): array
    {
        return [
            self::REQUEST_PENDING,
            self::REQUEST_APPROVED,
            self::REQUEST_REJECTED,
            self::REQUEST_CANCELLED,
        ];
    }

    public static function stepStatuses(): array
    {
        return [
            self::STEP_PENDING,
            self::STEP_APPROVED,
            self::STEP_REJECTED,
            self::STEP_SKIPPED,
        ];
    }

    public static function isValidRequestStatus(string $status): bool
    {
        return in_array($status, self::requestStatuses(), true);
    }

    public static function isValidStepStatus(string $status): bool
    {
        return in_array($status, self::stepStatuses(), true);
    }
}
