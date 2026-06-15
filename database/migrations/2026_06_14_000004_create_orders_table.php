<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->dateTime('order_date');
            $table->date('pickup_date');
            $table->enum('type', ['pickup', 'delivery'])->default('pickup');
            $table->enum('status', ['pending', 'confirmed', 'producing', 'ready', 'done', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('payment_proof')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
