<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Member;
use App\Models\Promotion;
use App\Models\RewardAchiever;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_members'   => Member::count(),
            'approved'        => Member::where('status', 'approved')->count(),
            'pending'         => Member::where('status', 'pending')->count(),
            'terminated'      => Member::where('status', 'terminated')->count(),
            'total_rewards'   => RewardAchiever::sum('reward_amount'),
            'active_promos'   => Promotion::active()->count(),
        ];

        $recentMembers  = Member::with('referrer')->latest()->take(5)->get();
        $recentRewards  = RewardAchiever::with(['member', 'promotion'])->latest('achieved_at')->take(5)->get();

        return view('admin::dashboard.index', compact('stats', 'recentMembers', 'recentRewards'));
    }
}
