<?php

namespace App\Console\Commands;

use App\Models\DayReport;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

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
            $query2 = User::query()->withCount(['orders' => function ($q) use ($date) {
                $q->whereDate('closing_date', $date);
            }])->orderByDesc('orders_count')->first(['name']);
            isset($query2) ? $data['best_waiter'] = $query2->name : $data['best_waiter'] = '-';

            $q3 = Dish::withSum('orders as total_quantity', 'dish_orders.quantity')->whereHas('orders', function ($q) use ($date) {
                    $q->whereDate('closing_date', $date);
                })->orderByDesc('total_quantity')->first(['name']);
            isset($query3) ? $data['best_dish'] = $query3->name : $data['best_dish'] = '-';

            DayReport::create($data);

            $this->line($data);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
