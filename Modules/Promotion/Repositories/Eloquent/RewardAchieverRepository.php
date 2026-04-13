<?php

namespace Modules\Promotion\Repositories\Eloquent;

use App\Models\RewardAchiever;
use App\Repositories\Interfaces\RewardAchieverRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RewardAchieverRepository implements RewardAchieverRepositoryInterface
{
    public function __construct(protected RewardAchiever $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['member', 'promotion'])
            ->when($filters['member_id'] ?? null, fn($q, $v) => $q->where('member_id', $v))
            ->when($filters['promotion_id'] ?? null, fn($q, $v) => $q->where('promotion_id', $v))
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->where('achieved_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn($q, $v) => $q->where('achieved_at', '<=', $v))
            ->latest('achieved_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(array $filters = []): Collection
    {
        return $this->model
            ->with(['member', 'promotion'])
            ->when($filters['member_id'] ?? null, fn($q, $v) => $q->where('member_id', $v))
            ->when($filters['promotion_id'] ?? null, fn($q, $v) => $q->where('promotion_id', $v))
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->where('achieved_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn($q, $v) => $q->where('achieved_at', '<=', $v))
            ->latest('achieved_at')
            ->get();
    }

    public function create(array $data): RewardAchiever
    {
        return $this->model->create($data);
    }

    public function firstOrCreate(array $attributes, array $values = []): RewardAchiever
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    public function countForMemberPromotion(int $memberId, int $promotionId, int $tierNumber): int
    {
        return $this->model
            ->where('member_id', $memberId)
            ->where('promotion_id', $promotionId)
            ->where('tier_number', $tierNumber)
            ->count();
    }
}
