<?php

namespace Modules\Promotion\Http\Controllers;

use App\Models\Member;
use App\Models\Promotion;
use App\Repositories\Interfaces\RewardAchieverRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Promotion\Http\Requests\PromotionRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RewardReportController extends Controller
{
    public function __construct(protected RewardAchieverRepositoryInterface $rewardRepo) {}

    public function promotions()
    {
        $promotions = Promotion::with('rewardTiers')
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('promotion::promotions.index', compact('promotions'));
    }

    public function createPromotion()
    {
        $promotion = new Promotion([
            'status' => 'draft',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ]);

        $tiers = $this->defaultTiers();

        return view('promotion::promotions.form', compact('promotion', 'tiers'));
    }

    public function storePromotion(PromotionRequest $request): RedirectResponse
    {
        $promotion = DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $promotion = Promotion::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
            ]);

            $this->syncTiers($promotion, $validated['tiers'] ?? []);

            return $promotion;
        });

        return redirect()
            ->route('promotions.edit', $promotion)
            ->with('success', 'Promotion created successfully.');
    }

    public function editPromotion(Promotion $promotion)
    {
        $promotion->load('rewardTiers');
        $tiers = $promotion->rewardTiers->sortBy('tier_number')->values()->all();

        if (count($tiers) !== 4) {
            $tiers = $this->defaultTiers();
        }

        return view('promotion::promotions.form', compact('promotion', 'tiers'));
    }

    public function updatePromotion(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        DB::transaction(function () use ($request, $promotion) {
            $validated = $request->validated();

            $promotion->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
            ]);

            $this->syncTiers($promotion, $validated['tiers'] ?? []);
        });

        return redirect()
            ->route('promotions.edit', $promotion)
            ->with('success', 'Promotion updated successfully.');
    }

    // ── Report Index ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $filters = $request->only(['member_id', 'promotion_id', 'date_from', 'date_to']);

        $rewards    = $this->rewardRepo->paginate($filters, 20);
        $promotions = Promotion::orderBy('name')->get();
        $members    = Member::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'referral_code']);

        return view('promotion::rewards.index', compact('rewards', 'filters', 'promotions', 'members'));
    }

    // ── Export CSV ────────────────────────────────────────────────────────
    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only(['member_id', 'promotion_id', 'date_from', 'date_to']);
        $rewards = $this->rewardRepo->all($filters);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reward_report_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($rewards) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Member Name', 'Member Email', 'Referral Code',
                'Promotion', 'Tier', 'Referral Count at Achievement',
                'Reward Amount (USD)', 'Achieved At',
            ]);

            foreach ($rewards as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->member->full_name ?? '—',
                    $r->member->email ?? '—',
                    $r->member->referral_code ?? '—',
                    $r->promotion->name ?? '—',
                    "Tier {$r->tier_number}",
                    $r->referral_count_at_achievement,
                    number_format($r->reward_amount, 2),
                    $r->achieved_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function syncTiers(Promotion $promotion, array $tiers): void
    {
        foreach ($tiers as $tier) {
            $promotion->rewardTiers()->updateOrCreate(
                ['tier_number' => $tier['tier_number']],
                [
                    'referral_count' => $tier['referral_count'],
                    'reward_amount' => $tier['reward_amount'],
                    'type' => $tier['type'],
                    'recurring_interval' => $tier['type'] === 'recurring'
                        ? ($tier['recurring_interval'] ?? null)
                        : null,
                ]
            );
        }
    }

    private function defaultTiers(): array
    {
        return [
            ['tier_number' => 1, 'referral_count' => 10, 'reward_amount' => 100, 'type' => 'fixed', 'recurring_interval' => null],
            ['tier_number' => 2, 'referral_count' => 50, 'reward_amount' => 500, 'type' => 'fixed', 'recurring_interval' => null],
            ['tier_number' => 3, 'referral_count' => 100, 'reward_amount' => 1000, 'type' => 'fixed', 'recurring_interval' => null],
            ['tier_number' => 4, 'referral_count' => 100, 'reward_amount' => 150, 'type' => 'recurring', 'recurring_interval' => 10],
        ];
    }
}
