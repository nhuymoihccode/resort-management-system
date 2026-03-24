<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration này thay thế toàn bộ các lệnh tinker ALTER TABLE đã chạy thủ công.
 * Bao gồm: booking payment fields, holding status, phone nullable.
 * Sau migrate:fresh --seed sẽ không cần chạy tinker thêm gì nữa.
 */
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

        // ── 3. users: phone nullable (thay cho migration riêng) ─────────────
        Schema::table('users', function (Blueprint $table) {
            // Dùng DB::statement vì change() cần doctrine/dbal
        });
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN phone VARCHAR(255) NULL
        ");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('orders', 'deposit_amount') ? 'deposit_amount' : null,
                Schema::hasColumn('orders', 'transfer_code')  ? 'transfer_code'  : null,
                Schema::hasColumn('orders', 'note')           ? 'note'           : null,
                Schema::hasColumn('orders', 'expires_at')     ? 'expires_at'     : null,
            ]));
        });

        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status
            ENUM('pending','confirmed','checked_in','checked_out','cancelled')
            DEFAULT 'pending'
        ");

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('bills', 'qr_image_url')    ? 'qr_image_url'    : null,
                Schema::hasColumn('bills', 'bank_payload')    ? 'bank_payload'    : null,
                Schema::hasColumn('bills', 'confirm_status')  ? 'confirm_status'  : null,
                Schema::hasColumn('bills', 'confirmed_at')    ? 'confirmed_at'    : null,
                Schema::hasColumn('bills', 'confirmed_by')    ? 'confirmed_by'    : null,
            ]));
        });

        DB::statement("
            ALTER TABLE bills
            MODIFY COLUMN payment_method ENUM('cash','card','transfer') DEFAULT 'cash'
        ");

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN phone VARCHAR(255) NOT NULL
        ");
    }
};