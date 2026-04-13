<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\RewardTier;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Promotion 1: Q1 2024 (already ended) ──────────────────────────
        $q1 = Promotion::firstOrCreate(
            ['name' => 'Q1 2024 Referral Campaign'],
            [
                'description' => 'Earn rewards by referring members during Q1 2024.',
                'start_date'  => '2024-01-01',
                'end_date'    => '2024-03-31',
                'status'      => 'ended',
            ]
        );
        $this->seedTiers($q1);

        // ── Promotion 2: Current active campaign ──────────────────────────
        $current = Promotion::firstOrCreate(
            ['name' => '2026 Annual Referral Drive'],
            [
                'description' => 'Our flagship annual referral program. Refer and earn big!',
                'start_date'  => '2026-01-01',
                'end_date'    => '2026-12-31',
                'status'      => 'active',
            ]
        );
        $this->seedTiers($current);

        $this->command->info('Promotions and reward tiers seeded.');
    }

    private function seedTiers(Promotion $promotion): void
    {
        $tiers = [
            [
                'tier_number'        => 1,
                'referral_count'     => 10,
                'reward_amount'      => 100.00,
                'type'               => 'fixed',
                'recurring_interval' => null,
            ],
            [
                'tier_number'        => 2,
                'referral_count'     => 50,
                'reward_amount'      => 500.00,
                'type'               => 'fixed',
                'recurring_interval' => null,
            ],
            [
                'tier_number'        => 3,
                'referral_count'     => 100,
                'reward_amount'      => 1000.00,
                'type'               => 'fixed',
                'recurring_interval' => null,
            ],
            [
                'tier_number'        => 4,
                'referral_count'     => 100,   // base milestone
                'reward_amount'      => 150.00,
                'type'               => 'recurring',
                'recurring_interval' => 10,    // every 10 extra referrals
            ],
        ];

        foreach ($tiers as $tier) {
            RewardTier::firstOrCreate(
                [
                    'promotion_id' => $promotion->id,
                    'tier_number'  => $tier['tier_number'],
                ],
                $tier + ['promotion_id' => $promotion->id]
            );
        }
    }
}
