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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->unsignedBigInteger('number_of_items')->default(0);
            $table->float('cost');
            $table->date('date_of_creation');
            $table->date('closing_date')->nullable();
            $table->unsignedBigInteger('user_id')->default(null);
            $table->timestamps();

            $table->index('user_id', 'orders_user_idx');
            $table->foreign('user_id', 'orders_user_fk')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
