<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('phone');
        $table->string('brand_name')->nullable();
        $table->text('address')->nullable();
        
        // تفاصيل التعاقد
        $table->date('contract_start_date')->nullable();
        $table->decimal('contract_value', 12, 2)->nullable();
        $table->enum('payment_cycle', ['one_time', 'weekly', 'monthly'])->default('monthly');
        $table->enum('status', ['active', 'paused', 'stopped'])->default('active');
        $table->date('next_payment_date')->nullable();
        
        $table->json('social_links')->nullable(); // بيحفظ الـ Array اللي جاية من الرياكت أوتوماتيك
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
