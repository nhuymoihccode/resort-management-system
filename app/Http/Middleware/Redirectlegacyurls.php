<?php

namespace App\Http\Middleware;

use App\Models\Room;
use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: Redirect 301 cho URL cũ dùng ID số nguyên.
 *
 * Xử lý 2 trường hợp:
 *   /rooms/5           → /rooms/phong-101           (301)
 *   /booking/42/...    → /booking/uuid-ngau-nhien/... (301)
 *
 * Đặt trong bootstrap/app.php hoặc routes/web.php theo hướng dẫn bên dưới.
 */
class RedirectLegacyUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path(); // không có leading slash

        // ── 1. /rooms/{số nguyên} → /rooms/{slug} ────────────────────────────
        if (preg_match('#^rooms/(\d+)$#', $path, $m)) {
            $room = Room::find($m[1]);
            if ($room) {
                return redirect()->route('rooms.show', $room->slug)->setStatusCode(301);
            }
        }

        // ── 2. /booking/{số nguyên}/{action} → /booking/{uuid}/{action} ──────
        if (preg_match('#^booking/(\d+)/(.+)$#', $path, $m)) {
            $order = Order::find($m[1]);
            if ($order) {
                // Giữ nguyên action (payment, cancel, status, cancel-holding, refresh-qr)
                return redirect(url('booking/' . $order->uuid . '/' . $m[2]))->setStatusCode(301);
            }
        }

        return $next($request);
    }
}