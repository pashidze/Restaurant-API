<?php

namespace App\Console\Commands;

use App\Models\DayReport;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DayReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:day-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда формирует отчет за день и вносит данные в БД';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $date = now()->format('Y-m-d');

            //Текущая дата и время
            $data['date'] = now()->format('Y-m-d H:i:s');

            //Запрос на подсчет количества заказов на текущий день
            $query1 = Order::where('closing_date', "'{$date}'")->count();
            $data['orders_count'] = $query1;

            //Запрос на поиск официанта, закрывшего больше всего заказов за текущий день
            $query2 = DB::table('orders')
                ->select(DB::raw('users.name, count(orders.number) as count'))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->where('orders.closing_date', "'{$date}'")
                ->groupBy('users.name')
                ->orderBy('count', 'desc')
                ->first();
            isset($query2) ? $data['best_waiter'] = $query2->name : $data['best_waiter'] = '-';

            //Запрос на поиск блюда, которое заказывали чаще всего за текущий день
            $query3 = DB::table('dish_orders')
                ->select(DB::raw('dishes.name, sum(dish_orders.quantity) as sum'))
                ->join('dishes', 'dish_orders.dish_id', '=', 'dishes.id')
                ->join('orders', 'orders.id', '=', 'dish_orders.order_id')
                ->where('orders.closing_date', "'{$date}'")
                ->groupBy('dishes.name')
                ->orderBy('sum', 'desc')
                ->first();
            isset($query3) ? $data['best_dish'] = $query3->name : $data['best_dish'] = '-';

            DayReport::create($data);

            $this->line($data);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
