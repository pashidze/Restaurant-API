<?php

namespace App\Services\Dish;

use App\Models\MenuCategory;
use Exception;

class ShowMenuService
{
    public function filter($data)
    {
        try {
            $query = MenuCategory::query()->with('dishes');

            isset($data['name']) ? $query->where('name', 'like', '%' . $data['name'] . '%') : null;
            isset($data['category_id']) ? $query->where('id', $data['category_id'])  : null;

            $page = $data['page'] ?? 1;
            $perPage = $data['per_page'] ?? 10;

            $orderBy = $data['sort_by'] ?? 'id';
            $orderDir = $data['sort_dir'] ?? 'asc';

            $query->orderBy($orderBy, $orderDir);

            return $query->paginate($perPage, ['*'], 'page', $page);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }
}
