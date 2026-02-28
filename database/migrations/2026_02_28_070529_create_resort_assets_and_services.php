<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. Rooms (Đã nâng cấp V2: Quản lý dọn dẹp và AI lock)
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->string('room_number')->unique();
            $table->enum('type', ['standard', 'suite', 'villa', 'bungalow'])->default('standard');
            $table->integer('price');
            $table->integer('capacity_adults')->default(2);
            $table->integer('capacity_children')->default(1);
            $table->string('view')->nullable();
            $table->integer('area')->nullable();
            $table->enum('status', ['available', 'occupied', 'cleaning', 'maintenance'])->default('available');
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_cleaned_at')->nullable();
            $table->foreignId('cleaned_by_staff_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 2. Services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->enum('unit', ['person', 'hour', 'turn'])->default('turn');
            $table->timestamps();
        });

        // 3. Promotions
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount_value');
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('services');
        Schema::dropIfExists('rooms');
    }
};