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
  Schema::create('leave_requests', function (Blueprint $table) {
            $table->id(); // BIGINT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('user_id')->constrained('users'); // FK -> users.id
            $table->string('pronoun', 10)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('division', 100)->nullable();
            $table->foreignId('leave_type_id')->constrained('leave_types'); // FK -> leave_types.id
            $table->text('leave_reason')->nullable();
            $table->date('start_date')->nullable();
            $table->tinyInteger('start_full_day')->default(1); // 1=เต็มวัน, 2=ครึ่งวัน
            $table->date('end_date')->nullable();
            $table->tinyInteger('end_full_day')->default(1);
            $table->string('contact', 255)->nullable();
            $table->json('files')->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamps(); // created_at & updated_at
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
