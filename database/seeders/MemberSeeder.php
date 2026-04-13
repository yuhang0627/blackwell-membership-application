<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AddressType;
use App\Models\Member;
use App\Models\Promotion;
use App\Models\RewardAchiever;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $residential = AddressType::where('name', 'Residential Address')->first();
        $correspondence = AddressType::where('name', 'Correspondence Address')->first();

        // ── Root member: Alice (no referrer) ─────────────────────────────
        $alice = Member::firstOrCreate(
            ['email' => 'alice@example.com'],
            [
                'first_name'    => 'Alice',
                'last_name'     => 'Anderson',
                'phone'         => '+60-12-3456789',
                'date_of_birth' => '1990-05-15',
                'gender'        => 'female',
                'nationality'   => 'Malaysian',
                'ic_number'     => '900515-01-1234',
                'referral_code' => 'ALICE001',
                'status'        => 'approved',
            ]
        );
        $this->addAddress($alice->id, $residential?->id ?? 1, 'No. 12, Jalan Bukit Bintang', null, 'Kuala Lumpur', 'Kuala Lumpur', '50200');

        // ── Level 1 under Alice: Bob ───────────────────────────────────────
        $bob = Member::firstOrCreate(
            ['email' => 'bob@example.com'],
            [
                'first_name'    => 'Bob',
                'last_name'     => 'Baker',
                'phone'         => '+60-11-9876543',
                'date_of_birth' => '1988-09-22',
                'gender'        => 'male',
                'nationality'   => 'Malaysian',
                'ic_number'     => '880922-02-5678',
                'referral_code' => 'BOB00001',
                'referred_by'   => $alice->id,
                'status'        => 'approved',
            ]
        );
        $this->addAddress($bob->id, $residential?->id ?? 1, 'No. 5, Jalan Ampang', null, 'Kuala Lumpur', 'Selangor', '68000');

        // ── Level 2 under Bob: Carol & David ──────────────────────────────
        $carol = Member::firstOrCreate(
            ['email' => 'carol@example.com'],
            [
                'first_name'    => 'Carol',
                'last_name'     => 'Chen',
                'phone'         => '+60-16-1234567',
                'date_of_birth' => '1995-03-10',
                'gender'        => 'female',
                'nationality'   => 'Malaysian',
                'ic_number'     => '950310-03-9012',
                'referral_code' => 'CAROL001',
                'referred_by'   => $bob->id,
                'status'        => 'approved',
            ]
        );
        $this->addAddress($carol->id, $residential?->id ?? 1, 'No. 88, Jalan Ipoh', null, 'Ipoh', 'Perak', '31400');

        $david = Member::firstOrCreate(
            ['email' => 'david@example.com'],
            [
                'first_name'    => 'David',
                'last_name'     => 'Tan',
                'phone'         => '+60-17-7654321',
                'date_of_birth' => '1992-11-30',
                'gender'        => 'male',
                'nationality'   => 'Malaysian',
                'ic_number'     => '921130-04-3456',
                'referral_code' => 'DAVID001',
                'referred_by'   => $bob->id,
                'status'        => 'approved',
            ]
        );
        $this->addAddress($david->id, $correspondence?->id ?? 2, 'No. 3, Jalan Dato Keramat', null, 'Penang', 'Penang', '10150');

        // ── Level 3 under David: Eve ──────────────────────────────────────
        $eve = Member::firstOrCreate(
            ['email' => 'eve@example.com'],
            [
                'first_name'    => 'Eve',
                'last_name'     => 'Lim',
                'phone'         => '+60-13-2468135',
                'date_of_birth' => '1998-07-04',
                'gender'        => 'female',
                'nationality'   => 'Malaysian',
                'ic_number'     => '980704-05-7890',
                'referral_code' => 'EVE00001',
                'referred_by'   => $david->id,
                'status'        => 'pending',
            ]
        );
        $this->addAddress($eve->id, $residential?->id ?? 1, 'No. 21, Jalan Segambut', null, 'Kuala Lumpur', 'Kuala Lumpur', '51200');

        // Add more members referred by Alice to simulate reward tiers
        $this->createBulkReferrals($alice, $residential?->id ?? 1);

        // ── Sample reward achievers ────────────────────────────────────────
        $this->seedSampleRewards($alice, $bob);

        $this->command->info('Members and sample rewards seeded.');
    }

    private function addAddress(int $memberId, int $typeId, string $line1, ?string $line2, string $city, string $state, string $postcode): void
    {
        Address::firstOrCreate(
            ['member_id' => $memberId, 'address_type_id' => $typeId],
            [
                'address_line_1' => $line1,
                'address_line_2' => $line2,
                'city'           => $city,
                'state'          => $state,
                'postcode'       => $postcode,
                'country'        => 'Malaysia',
            ]
        );
    }

    private function createBulkReferrals(Member $referrer, int $addressTypeId): void
    {
        $cities = ['Kuala Lumpur', 'Petaling Jaya', 'Subang Jaya', 'Shah Alam', 'Cyberjaya'];
        $states = ['Kuala Lumpur', 'Selangor', 'Selangor', 'Selangor', 'Selangor'];

        for ($i = 1; $i <= 12; $i++) {
            $member = Member::firstOrCreate(
                ['email' => "member{$i}@example.com"],
                [
                    'first_name'    => "Member",
                    'last_name'     => "No{$i}",
                    'phone'         => "+60-1{$i}-1234567",
                    'date_of_birth' => '1990-01-01',
                    'gender'        => 'male',
                    'nationality'   => 'Malaysian',
                    'ic_number'     => "900101-0{$i}-000{$i}",
                    'referral_code' => 'MBR' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'referred_by'   => $referrer->id,
                    'status'        => 'approved',
                ]
            );

            $cityIdx = ($i - 1) % count($cities);
            $this->addAddress(
                $member->id,
                $addressTypeId,
                "No. {$i}, Jalan Sample",
                null,
                $cities[$cityIdx],
                $states[$cityIdx],
                '47500'
            );
        }
    }

    private function seedSampleRewards(Member $alice, Member $bob): void
    {
        $promotion = Promotion::where('status', 'active')->first();
        if (! $promotion) {
            return;
        }

        $rewards = [
            [
                'member_id'                     => $alice->id,
                'promotion_id'                  => $promotion->id,
                'tier_number'                   => 1,
                'referral_count_at_achievement' => 10,
                'reward_amount'                 => 100.00,
                'achieved_at'                   => '2026-02-01',
            ],
            [
                'member_id'                     => $alice->id,
                'promotion_id'                  => $promotion->id,
                'tier_number'                   => 2,
                'referral_count_at_achievement' => 12,
                'reward_amount'                 => 100.00,
                'achieved_at'                   => '2026-02-15',
            ],
            [
                'member_id'                     => $bob->id,
                'promotion_id'                  => $promotion->id,
                'tier_number'                   => 1,
                'referral_count_at_achievement' => 10,
                'reward_amount'                 => 100.00,
                'achieved_at'                   => '2026-03-01',
            ],
        ];

        foreach ($rewards as $reward) {
            RewardAchiever::firstOrCreate(
                [
                    'member_id'    => $reward['member_id'],
                    'promotion_id' => $reward['promotion_id'],
                    'tier_number'  => $reward['tier_number'],
                    'referral_count_at_achievement' => $reward['referral_count_at_achievement'],
                ],
                $reward
            );
        }
    }
}
