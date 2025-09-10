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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image')->nullable();
            $table->text('composition')->nullable();
            $table->float('calories')->default(0);
            $table->float('price')->default(0);
            $table->softDeletes();
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->index('category_id', 'category_dishes_idx');
            $table->foreign('category_id', 'category_dishes_fk')->references('id')->on('menu_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
