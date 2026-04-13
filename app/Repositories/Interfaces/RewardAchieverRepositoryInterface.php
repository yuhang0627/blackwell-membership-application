<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RewardAchieverRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function all(array $filters = []): Collection;

    public function create(array $data): mixed;

    public function firstOrCreate(array $attributes, array $values = []): mixed;

    public function countForMemberPromotion(int $memberId, int $promotionId, int $tierNumber): int;
}
