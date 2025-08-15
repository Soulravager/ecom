<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('user_id')->unsigned();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // pending/completed/cancelled
            $table->string('payment_type')->nullable();
            $table->string('payment_id', 20)->nullable(); // random transaction id
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
