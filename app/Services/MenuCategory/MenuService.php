<?php

namespace App\Services\MenuCategory;

use App\Models\MenuCategory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();

            //Сохранение картинки в хранилище и добавление записи в БД
            if(isset($data['image']))
            {
                $image = Storage::disk('public')->put('app/public/Images/MenuCategory', $data['image']);
                $data['image'] = $image;
            }
            $category = MenuCategory::create($data);

            DB::commit();

            //Объект для вывода в JSON
            return $category;
        } catch (Exception $exception) {
            DB::rollBack();

            //Удаление картинки, если что-то пошло не так
            if(isset($image) && Storage::exists($image)) {
                Storage::delete($image);
            }

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function update($category, $data)
    {
        try{
            DB::beginTransaction();

            $oldImg = $category->image;

            //Если меняется картинка, то нужно внести её в хранилище и удалить оттуда старую
            if(isset($data['image']))
            {
                $newImg = Storage::disk('public')->put('/Images/MenuCategory', $data['image']);
                $data['image'] = $newImg;
                $category->update($data);
                Storage::delete($oldImg);
            } else {
                $category->update($data);
            }

            DB::commit();

            //Объект для вывода в JSON
            return $category;
        } catch (Exception $exception)
        {
            DB::rollBack();

            //Удаление картинки, если что-то пошло не так
            if(isset($newImg) && Storage::exists($newImg)) {
                Storage::delete($newImg);
            }

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function delete($category)
    {
        try {
            DB::beginTransaction();

            //Удаление картинок всех связанных блюд
            foreach($category->dishes as $dish){
                Storage::delete($dish->image);
            }

            $category->delete(); //удаление картинки в модели

            DB::commit();

            return true;
        } catch (Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function filter($data)
    {
        try {
            $query = MenuCategory::query();

            isset($data['name']) ? $query->where('name', 'like', '%' . $data['name'] . '%'): '';

            $orderBy = $data['sort_by'] ?? 'id';
            $orderDir = $data['sort_dir'] ?? 'asc';

            $query->orderBy($orderBy, $orderDir);

            $menu = $query->paginate(10);

            return $menu;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }
}
