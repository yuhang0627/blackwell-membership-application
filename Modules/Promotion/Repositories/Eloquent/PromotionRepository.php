<?php

namespace Modules\Promotion\Repositories\Eloquent;

use App\Models\Promotion;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PromotionRepository implements PromotionRepositoryInterface
{
    public function __construct(protected Promotion $model) {}

    public function all(): Collection
    {
        return $this->model->with('rewardTiers')->latest()->get();
    }

    public function find(int $id): Promotion
    {
        return $this->model->with('rewardTiers')->findOrFail($id);
    }

    public function create(array $data): Promotion
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Promotion
    {
        $promotion = $this->model->findOrFail($id);
        $promotion->update($data);
        return $promotion->fresh();
    }

    public function getActivePromotions(): Collection
    {
        return $this->model->active()->with('rewardTiers')->get();
    }
}
