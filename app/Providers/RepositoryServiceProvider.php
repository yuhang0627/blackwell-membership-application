<?php

namespace App\Providers;

use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use App\Repositories\Interfaces\RewardAchieverRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Modules\Member\Repositories\Eloquent\MemberRepository;
use Modules\Promotion\Repositories\Eloquent\PromotionRepository;
use Modules\Promotion\Repositories\Eloquent\RewardAchieverRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(PromotionRepositoryInterface::class, PromotionRepository::class);
        $this->app->bind(RewardAchieverRepositoryInterface::class, RewardAchieverRepository::class);
    }
}
