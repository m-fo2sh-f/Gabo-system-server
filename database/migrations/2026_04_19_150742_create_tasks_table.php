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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        
        // العلاقات
        $table->foreignId('client_id')->constrained()->cascadeOnDelete();
        $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
        $table->foreignId('task_type_id')->constrained('task_types')->restrictOnDelete();

        $table->decimal('price', 10, 2)->default(0); // سعر البيع
        $table->decimal('cost', 10, 2)->default(0);  // التكلفة
        
        $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
