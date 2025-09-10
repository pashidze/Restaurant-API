<?php

namespace App\Services\Dish;

use App\Models\Dish;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DishService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();

            //Сохранение картинки в хранилище и добавление записи в БД
            if(isset($data['image']))
            {
                $imagePatch = Storage::disk('public')->put('/Images/Dishes', $data['image']);
                $data['image'] = $imagePatch;
            }
            $dish = Dish::create($data);

            DB::commit();

            //Объект для вывода в JSON
            return $dish;
        } catch (Exception $exception) {
            DB::rollBack();

            //Удаление картинки, если что-то пошло не так
            if(isset($imagePatch) && Storage::exists($imagePatch)) {
                Storage::delete($imagePatch);
            }

            throw new Exception($exception->getMessage(), 500);
        }

    }

    public function update($dish, $data)
    {
        try {
            DB::beginTransaction();

            $oldImg = $dish->image;

            //Если меняется картинка, то нужно внести её в хранилище и удалить оттуда старую
            if(isset($data['image'])) {
                $newImg = Storage::disk('public')->put('/Images/Dishes', $data['image']);
                $data['image'] = $newImg;
                $dish->update($data);
                Storage::delete($oldImg);
            } else {
                $dish->update($data);
            }

            DB::commit();

            //Объект для вывода в JSON
            return $dish;
        } catch (Exception $exception) {
            DB::rollBack();

            //Удаление картинки, если что-то пошло не так
            if(isset($newImg) && Storage::exists($newImg)) {
                Storage::delete($newImg);
            }

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function delete($dish)
    {
        try {
            DB::beginTransaction();

            $dish->delete();

            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function filter($data)
    {
        try {
            $query = Dish::query();

            isset($data['name']) ? $query->where('name', 'like', '%' . $data['name'] . '%') : null;
            isset($data['composition']) ? $query->where('composition', 'like', '%' . $data['composition'] . '%') : null;
            isset($data['category_id']) ? $query->where('category_id', $data['category_id']) : null;

            $sortBy = $data['sort_by'] ?? 'id';
            $sortDir = $data['sort_dir'] ?? 'asc';

            $page = $data['page'] ?? 1;
            $perPage = $data['per_page'] ?? 10;

            $query->orderBy($sortBy, $sortDir);

            return $query->paginate($perPage, ['*'], 'page', $page);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }
}
