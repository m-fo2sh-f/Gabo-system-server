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
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('phone')->nullable();
        $table->foreignId('job_title_id')->constrained('job_titles')->restrictOnDelete();
        $table->enum('employment_type', ['full_time', 'part_time', 'internship'])->default('full_time');
        
        // حسابات الفريلانس والراتب
        $table->decimal('base_salary', 10, 2)->nullable();
        $table->boolean('is_freelance')->default(false);
        $table->decimal('commission_rate', 5, 2)->nullable(); // نسبة مئوية
        
        $table->enum('status', ['active', 'paused', 'stopped'])->default('active');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
