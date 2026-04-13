<?php

namespace App\Repositories\Interfaces;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MemberRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function all(array $filters = []): Collection;

    public function find(int $id): Member;

    public function create(array $data): Member;

    public function update(int $id, array $data): Member;

    public function delete(int $id): bool;

    public function findByReferralCode(string $code): ?Member;

    public function getReferralTree(Member $member): array;
}
