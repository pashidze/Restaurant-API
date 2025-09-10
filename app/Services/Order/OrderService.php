<?php

namespace App\Services\Order;

use App\Models\Dish;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create($data)
    {
        try{
            DB::beginTransaction();

            $data['number'] = $this->findNumberOrder();
            $data['number_of_items'] = 0;
            $data['date_of_creation'] = date('Y-m-d');
            $data['cost'] = 0.0;
            $data['user_id'] = auth()->id();

            $order = Order::create($data);

            DB::commit();

            //Объект для JSON
            return $order;
        } catch (Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function update($order, $data)
    {
        try{
            DB::beginTransaction();

            $merged = $this->mergeData($data);

            [$attachData, $cost] = $this->attachData($merged);

            unset($data['list_of_dishes']);
            $data['number_of_items'] = count($merged);
            $data['cost'] = $cost;

            $order->update($data);
            $order->dishes()->sync($attachData);

            DB::commit();

            return $order;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function close($order)
    {
        try {
            DB::beginTransaction();

            //Закрытие заказа
            $data['closing_date'] = date('Y-m-d');
            $order->update($data);

            DB::commit();

            //Объект для JSON
            return $order;
        } catch (Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function delete($order)
    {
        try {
            DB::beginTransaction();

            $order->delete();

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
            $query = Order::query()->with(['dishes', 'user']);

            isset($data['number']) ? $query->where('number', 'like', '%' . $data['number'] . '%'): null;
            isset($data['waiter_id']) ? $query->where('user_id', $data['waiter_id']): null;
            isset($data['cost']) ? $query->where('cost', $data['cost']): null;

            $page = $data['page'] ?? 1;
            $perPage = $data['perPage'] ?? 10;

            $orderBy = $data['sort_by'] ?? 'id';
            $orderDir = $data['sort_dir'] ?? 'asc';

            $query->orderBy($orderBy, $orderDir);

            return $query->paginate($perPage, ['*'], 'page', $page);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }

    protected function findNumberOrder()
    {
        $day = date('d');
        $month = date('m');

        //поиск номера последнего заказа на текущий день
        $last = Order::where('number', 'like', "Заказ-{$day}{$month}-%")->orderBy('number', 'desc')->first();

        //Если заказы в текущий день уже были, то определяется следующий номер, в противном случае нумерация с 0001
        if($last) {
            $lastNumber = (int) substr($last->number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "Заказ-{$day}{$month}-{$nextNumber}";
    }

    protected function mergeData($data)
    {
        //Получение списка блюд в заказе и их количества
        /* Памятка: Может возникнуть ситуация, когда заказ будет иметь вид типа:
        блюдо: хлеб, количество: 1
        блюдо: хлеб, количество: 2
        блюдо: хлеб, количество: 1
        Это всё одна позиция, которую нужно объединить.
        Цикл проходит по массиву блюд заказа и переносит в другой массив уникальные значения, повторные суммирует.*/
        $listDish = $data['list_of_dishes'];
        $merged = [];
        foreach ($listDish as $item) {
            $dish_id = $item['dish_id'];
            $quantity = (int) $item['quantity'];
            isset($merged[$dish_id]) ? $merged[$dish_id] += $quantity : $merged[$dish_id] = $quantity;
        }

        return $merged;
    }

    protected function attachData($merged)
    {
        //Подготовка данных для attach()
        /*Памятка: так как изначально в коллекции только наименования блюд, то нужно достать их id.
          Из БД выгружается коллекция блюд, присутствующих в заказе.
          Далее в цикле для каждого блюда создаётся объект, позволяющий получить id блюда для связи в дальнейшем.*/
        $dishes = Dish::whereIn('id', array_keys($merged))->get()->keyBy('id');
        $attachData = [];
        $cost = 0;
        foreach ($merged as $dish_id => $quantity) {
            $dish = $dishes[$dish_id] ?? null;
            if ($dish) {
                $attachData[$dish->id] = ['quantity' => $quantity];
                $cost += $quantity * $dish->price;
            }
        }

        return [$attachData, $cost];
    }
}
