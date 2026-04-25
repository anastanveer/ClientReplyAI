<?php

namespace App\Services\Usage;

use App\Models\UsageLimit;
use App\Models\User;
use App\Services\Usage\Exceptions\DailyLimitExceededException;
use Carbon\CarbonImmutable;

class UsageLimitService
{
    public function freeReplyLimit(): int
    {
        return 10;
    }

    public function currentReplyUsage(User $user): int
    {
        return $this->todayUsageRecord($user)?->replies_generated ?? 0;
    }

    public function remainingReplies(User $user): ?int
    {
        if ($this->isUnlimitedPlan($user)) {
            return null;
        }

        return max($this->freeReplyLimit() - $this->currentReplyUsage($user), 0);
    }

    public function ensureCanGenerate(User $user): void
    {
        if ($this->isUnlimitedPlan($user)) {
            return;
        }

        if ($this->currentReplyUsage($user) >= $this->freeReplyLimit()) {
            throw new DailyLimitExceededException(
                sprintf(
                    'You have reached your free plan limit of %d replies for today. Please come back tomorrow or upgrade later for more usage.',
                    $this->freeReplyLimit(),
                ),
            );
        }
    }

    public function incrementRepliesGenerated(User $user): UsageLimit
    {
        $record = $this->todayUsageRecord($user);

        if ($record === null) {
            try {
                $record = UsageLimit::query()->create([
                    'user_id'             => $user->id,
                    'usage_date'          => $this->todayDateFor($user),
                    'replies_generated'   => 0,
                    'saved_replies_count' => 0,
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                $record = $this->todayUsageRecord($user);
            }
        }

        $record->increment('replies_generated');

        return $record->fresh();
    }

    /**
     * @return array{used:int, limit:int|null, remaining:int|null}
     */
    public function usageSummary(User $user): array
    {
        $used = $this->currentReplyUsage($user);

        return [
            'used' => $used,
            'limit' => $this->isUnlimitedPlan($user) ? null : $this->freeReplyLimit(),
            'remaining' => $this->remainingReplies($user),
        ];
    }

    protected function todayUsageRecord(User $user): ?UsageLimit
    {
        return UsageLimit::query()
            ->where('user_id', $user->id)
            ->whereDate('usage_date', $this->todayDateFor($user))
            ->first();
    }

    protected function todayDateFor(User $user): string
    {
        return CarbonImmutable::now($user->timezone ?: 'UTC')->toDateString();
    }

    protected function isUnlimitedPlan(User $user): bool
    {
        return in_array(strtolower($user->plan), ['pro', 'premium'], true);
    }
}
