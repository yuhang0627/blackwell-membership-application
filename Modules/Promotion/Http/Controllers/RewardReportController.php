<?php

namespace Modules\Promotion\Http\Controllers;

use App\Models\Member;
use App\Models\Promotion;
use App\Repositories\Interfaces\RewardAchieverRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RewardReportController extends Controller
{
    public function __construct(protected RewardAchieverRepositoryInterface $rewardRepo) {}

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
}
