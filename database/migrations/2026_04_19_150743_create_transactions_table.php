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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        
        $table->enum('type', ['income', 'expense']);
        $table->string('category'); 
        $table->decimal('amount', 12, 2);
        $table->string('payment_method')->default('cash');
        $table->date('transaction_date');
        
        
        $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
        $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
        $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
        

        // مين من الأدمنز اللي سجل المعاملة (صاحب الشركة)
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
        
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
