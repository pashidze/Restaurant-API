<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dish_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('quantity')->default(1);
            $table->timestamps();

            $table->index('order_id', 'order_id_idx');
            $table->index('dish_id', 'dish_id_idx');
            $table->foreign('order_id', 'order_id_fk')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dish_id', 'dish_id_fk')->references('id')->on('dishes')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_orders');
    }
};
