<?php

namespace App\Repositories\Interfaces;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;

interface PromotionRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): Promotion;

    public function create(array $data): Promotion;

    public function update(int $id, array $data): Promotion;

    public function getActivePromotions(): Collection;
}
