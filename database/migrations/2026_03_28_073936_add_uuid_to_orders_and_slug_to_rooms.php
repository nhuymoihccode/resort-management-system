<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. orders: thêm uuid ─────────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // Backfill uuid cho data cũ
        DB::table('orders')->whereNull('uuid')->orderBy('id')->each(function ($order) {
            DB::table('orders')->where('id', $order->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        });

        // Sau backfill mới NOT NULL
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        // ── 2. rooms: thêm slug ──────────────────────────────────────────────
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('room_number');
        });

        // Backfill slug từ room_number, đảm bảo unique
        DB::table('rooms')->whereNull('slug')->orderBy('id')->each(function ($room) {
            $base    = 'phong-' . Str::slug($room->room_number);
            $slug    = $base;
            $counter = 1;

            while (DB::table('rooms')->where('slug', $slug)->where('id', '!=', $room->id)->exists()) {
                $slug = $base . '-' . $counter++;
            }

            DB::table('rooms')->where('id', $room->id)->update(['slug' => $slug]);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn(Blueprint $t) => $t->dropColumn('uuid'));
        Schema::table('rooms',  fn(Blueprint $t) => $t->dropColumn('slug'));
    }
};