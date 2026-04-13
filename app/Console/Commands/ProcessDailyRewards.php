<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Promotion;
use App\Models\RewardAchiever;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessDailyRewards extends Command
{
    protected $signature   = 'rewards:process-daily {--dry-run : Preview without saving}';
    protected $description = 'Process daily referral rewards for all active promotions.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting daily reward processing — ' . now()->toDateTimeString());

        // Fetch all active promotions
        $activePromotions = Promotion::active()->with('rewardTiers')->get();

        if ($activePromotions->isEmpty()) {
            $this->warn('No active promotions found.');
            return self::SUCCESS;
        }

        $this->info("Found {$activePromotions->count()} active promotion(s).");

        foreach ($activePromotions as $promotion) {
            $this->processPromotion($promotion, $isDryRun);
        }

        $this->info('Daily reward processing complete.');
        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────
    private function processPromotion(Promotion $promotion, bool $isDryRun): void
    {
        $this->line("Processing promotion: [{$promotion->id}] {$promotion->name}");

        // Count referrals per member that were registered during the promotion window
        $members = Member::withCount([
            'referrals as referrals_count' => function ($query) use ($promotion) {
                $query->whereBetween('created_at', [
                    $promotion->start_date->startOfDay(),
                    $promotion->end_date->endOfDay(),
                ]);
            },
        ])->having('referrals_count', '>', 0)->get();

        $this->line("  Members with referrals: {$members->count()}");

        $totalNewRewards = 0;

        foreach ($members as $member) {
            $newRewards = $this->processMemberRewards(
                $member,
                $promotion,
                (int) $member->referrals_count,
                $isDryRun
            );
            $totalNewRewards += $newRewards;
        }

        $this->info("  Promotion [{$promotion->name}]: {$totalNewRewards} new reward(s) issued.");
    }

    // ─────────────────────────────────────────────────────────────────────
    private function processMemberRewards(
        Member    $member,
        Promotion $promotion,
        int       $referralCount,
        bool      $isDryRun
    ): int {
        $newCount = 0;

        foreach ($promotion->rewardTiers as $tier) {
            if ($tier->type === 'fixed') {
                // One-time reward when milestone is reached
                if ($referralCount >= $tier->referral_count) {
                    $exists = RewardAchiever::where([
                        'member_id'    => $member->id,
                        'promotion_id' => $promotion->id,
                        'tier_number'  => $tier->tier_number,
                    ])->exists();

                    if (! $exists) {
                        $this->outputReward($member, $tier->tier_number, $tier->referral_count, $tier->reward_amount, $isDryRun);

                        if (! $isDryRun) {
                            RewardAchiever::create([
                                'member_id'                   => $member->id,
                                'promotion_id'                => $promotion->id,
                                'tier_number'                 => $tier->tier_number,
                                'referral_count_at_achievement' => $tier->referral_count,
                                'reward_amount'               => $tier->reward_amount,
                                'achieved_at'                 => today(),
                            ]);
                        }
                        $newCount++;
                    }
                }
            } elseif ($tier->type === 'recurring' && $tier->recurring_interval > 0) {
                // Recurring reward: every N referrals beyond the base milestone
                if ($referralCount > $tier->referral_count) {
                    $extraReferrals = $referralCount - $tier->referral_count;
                    $totalEarned    = (int) floor($extraReferrals / $tier->recurring_interval);

                    $alreadyGiven = RewardAchiever::where([
                        'member_id'    => $member->id,
                        'promotion_id' => $promotion->id,
                        'tier_number'  => $tier->tier_number,
                    ])->count();

                    $toGive = $totalEarned - $alreadyGiven;

                    for ($i = 0; $i < $toGive; $i++) {
                        $countAtAchievement = $tier->referral_count
                            + ($tier->recurring_interval * ($alreadyGiven + $i + 1));

                        $this->outputReward($member, $tier->tier_number, $countAtAchievement, $tier->reward_amount, $isDryRun);

                        if (! $isDryRun) {
                            RewardAchiever::create([
                                'member_id'                   => $member->id,
                                'promotion_id'                => $promotion->id,
                                'tier_number'                 => $tier->tier_number,
                                'referral_count_at_achievement' => $countAtAchievement,
                                'reward_amount'               => $tier->reward_amount,
                                'achieved_at'                 => today(),
                            ]);
                        }
                        $newCount++;
                    }
                }
            }
        }

        return $newCount;
    }

    // ─────────────────────────────────────────────────────────────────────
    private function outputReward(Member $member, int $tier, int $count, float $amount, bool $isDryRun): void
    {
        $prefix = $isDryRun ? '[DRY-RUN] ' : '';
        $this->line(
            "    {$prefix}Reward → Member: {$member->full_name} | Tier {$tier} | {$count} referrals | USD " .
            number_format($amount, 2)
        );
    }
}
