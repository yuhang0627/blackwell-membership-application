<?php

namespace Modules\Member\Repositories\Eloquent;

use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MemberRepository implements MemberRepositoryInterface
{
    public function __construct(protected Member $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['referrer', 'addresses.addressType'])
            ->search($filters['search'] ?? null)
            ->filterByStatus($filters['status'] ?? null)
            ->filterByReferrer($filters['referral_code'] ?? null)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(array $filters = []): Collection
    {
        return $this->model
            ->with(['referrer', 'addresses.addressType'])
            ->search($filters['search'] ?? null)
            ->filterByStatus($filters['status'] ?? null)
            ->filterByReferrer($filters['referral_code'] ?? null)
            ->latest()
            ->get();
    }

    public function find(int $id): Member
    {
        return $this->model
            ->with([
                'referrer',
                'referrals',
                'addresses.addressType',
                'addresses.documents',
                'documents',
                'rewardAchievers.promotion',
            ])
            ->findOrFail($id);
    }

    public function create(array $data): Member
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Member
    {
        $member = $this->model->findOrFail($id);
        $member->update($data);
        return $member->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }

    public function findByReferralCode(string $code): ?Member
    {
        return $this->model->where('referral_code', $code)->first();
    }

    public function getReferralTree(Member $member): array
    {
        return $member->getReferralTree();
    }
}
