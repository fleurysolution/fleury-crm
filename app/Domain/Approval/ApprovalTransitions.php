<?php

namespace App\Domain\Approval;

final class ApprovalTransitions
{
    /**
     * Request-level transition map.
     */
    private const REQUEST_TRANSITIONS = [
        ApprovalStatus::REQUEST_PENDING => [
            ApprovalStatus::REQUEST_APPROVED,
            ApprovalStatus::REQUEST_REJECTED,
            ApprovalStatus::REQUEST_CANCELLED,
        ],
        ApprovalStatus::REQUEST_APPROVED => [],
        ApprovalStatus::REQUEST_REJECTED => [],
        ApprovalStatus::REQUEST_CANCELLED => [],
    ];

    /**
     * Step-level transition map.
     */
    private const STEP_TRANSITIONS = [
        ApprovalStatus::STEP_PENDING => [
            ApprovalStatus::STEP_APPROVED,
            ApprovalStatus::STEP_REJECTED,
            ApprovalStatus::STEP_SKIPPED,
        ],
        ApprovalStatus::STEP_APPROVED => [],
        ApprovalStatus::STEP_REJECTED => [],
        ApprovalStatus::STEP_SKIPPED => [],
    ];

    public static function canTransitionRequest(string $from, string $to): bool
    {
        return in_array($to, self::REQUEST_TRANSITIONS[$from] ?? [], true);
    }

    public static function canTransitionStep(string $from, string $to): bool
    {
        return in_array($to, self::STEP_TRANSITIONS[$from] ?? [], true);
    }
}
