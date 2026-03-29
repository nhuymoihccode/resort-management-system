<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. orders: thêm status 'holding' + các cột payment ─────────────
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status
            ENUM('holding','pending','confirmed','checked_in','checked_out','cancelled')
            DEFAULT 'pending'
        ");

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'deposit_amount')) {
                $table->unsignedBigInteger('deposit_amount')->default(0)->after('total_price');
            }
            if (! Schema::hasColumn('orders', 'transfer_code')) {
                $table->string('transfer_code', 20)->nullable()->unique()->after('deposit_amount');
            }
            if (! Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('transfer_code');
            }
            if (! Schema::hasColumn('orders', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('note');
            }
        });

        // ── 2. bills: đổi payment_method sang VARCHAR + thêm cột payment ───
        DB::statement("
            ALTER TABLE bills
            MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'cash'
        ");

        Schema::table('bills', function (Blueprint $table) {
            if (! Schema::hasColumn('bills', 'qr_image_url')) {
                $table->text('qr_image_url')->nullable()->after('payment_date');
            }
            if (! Schema::hasColumn('bills', 'bank_payload')) {
                $table->json('bank_payload')->nullable()->after('qr_image_url');
            }
            if (! Schema::hasColumn('bills', 'confirm_status')) {
                $table->enum('confirm_status', ['pending', 'confirmed', 'failed'])
                      ->default('pending')->after('bank_payload');
            }
            if (! Schema::hasColumn('bills', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('confirm_status');
            }
            if (! Schema::hasColumn('bills', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_at');
            }
        });

        // ── 3. users: phone nullable ─────────────────────────────────────────
        DB::statement("ALTER TABLE users MODIFY COLUMN phone VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // ── orders: xóa từng cột an toàn ────────────────────────────────────
        foreach (['deposit_amount', 'transfer_code', 'note', 'expires_at'] as $col) {
            if (Schema::hasColumn('orders', $col)) {
                Schema::table('orders', fn($t) => $t->dropColumn($col));
            }
        }

        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status
            ENUM('pending','confirmed','checked_in','checked_out','cancelled')
            DEFAULT 'pending'
        ");

        // ── bills: drop foreign key bằng raw SQL IF EXISTS ───────────────────
        // Không dùng dropForeign() hay dropForeignIfExists() vì không tương thích
        // mọi version Laravel — dùng MySQL information_schema để check an toàn
        $fkExists = DB::selectOne("
            SELECT COUNT(*) as cnt
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'bills'
            AND CONSTRAINT_NAME = 'bills_confirmed_by_foreign'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        if ($fkExists && $fkExists->cnt > 0) {
            DB::statement("ALTER TABLE bills DROP FOREIGN KEY bills_confirmed_by_foreign");
        }

        // Xóa từng cột bills an toàn
        foreach (['confirmed_by', 'confirmed_at', 'confirm_status', 'bank_payload', 'qr_image_url'] as $col) {
            if (Schema::hasColumn('bills', $col)) {
                Schema::table('bills', fn($t) => $t->dropColumn($col));
            }
        }

        DB::statement("
            ALTER TABLE bills
            MODIFY COLUMN payment_method ENUM('cash','card','transfer') DEFAULT 'cash'
        ");

        // ── users: phone → NOT NULL ──────────────────────────────────────────
        // Dùng CONCAT('unknown-', id) để tránh lỗi unique constraint
        DB::statement("UPDATE users SET phone = CONCAT('unknown-', id) WHERE phone IS NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN phone VARCHAR(255) NOT NULL");
    }
};