<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. Loyalty Tiers (Phải có trước để Customer tham chiếu)
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->bigInteger('min_spend')->default(0);
            $table->integer('discount_percent')->default(0);
            $table->json('perks')->nullable();
            $table->timestamps();
        });

        // 2. Resort Info
        Schema::create('resort_info', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        // 3. Zones
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resort_info_id')->constrained('resort_info')->onDelete('cascade');
            $table->string('name'); 
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Customers (Đã nâng cấp V2: full_name, loyalty, ai_preferences)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('loyalty_tier_id')->default(1)->constrained('loyalty_tiers');
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('id_card')->nullable();
            $table->bigInteger('total_spent')->default(0);
            $table->json('ai_preferences')->nullable();
            $table->timestamps();
        });

        // 5. Staffs
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resort_info_id')->constrained('resort_info')->onDelete('cascade');
            $table->string('name');
            $table->string('position');
            $table->integer('salary')->default(0);
            $table->date('started_at');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('staffs');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('resort_info');
        Schema::dropIfExists('loyalty_tiers');
    }
};