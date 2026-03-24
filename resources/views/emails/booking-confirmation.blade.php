<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Xác nhận đặt phòng</title>
<style>
  body { margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
  .hero { background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%); padding:40px 32px; text-align:center; }
  .hero-logo { font-size:13px; font-weight:700; letter-spacing:.25em; color:#fbbf24; text-transform:uppercase; margin-bottom:16px; }
  .hero h1 { margin:0; font-size:26px; color:#fff; font-weight:700; line-height:1.3; }
  .hero p { margin:8px 0 0; color:#94a3b8; font-size:14px; }
  .badge { display:inline-block; background:#d97706; color:#fff; font-size:12px; font-weight:700; letter-spacing:.1em; padding:4px 14px; border-radius:99px; text-transform:uppercase; margin-top:16px; }
  .body { padding:32px; }
  .greeting { font-size:16px; color:#475569; margin-bottom:24px; }
  .greeting strong { color:#1e293b; }
  .section-title { font-size:11px; font-weight:700; letter-spacing:.15em; text-transform:uppercase; color:#94a3b8; margin:0 0 12px; }
  .card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:20px; }
  .row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #e2e8f0; }
  .row:last-child { border-bottom:none; }
  .row-label { font-size:13px; color:#64748b; }
  .row-value { font-size:14px; font-weight:600; color:#1e293b; text-align:right; }
  .highlight-card { background:#fffbeb; border:1.5px solid #fde68a; border-radius:12px; padding:20px; margin-bottom:20px; }
  .amount-big { font-size:28px; font-weight:800; color:#d97706; }
  .amount-label { font-size:12px; color:#92400e; margin-top:2px; }
  .code-box { background:#1e293b; color:#fbbf24; font-family:monospace; font-size:20px; font-weight:700; letter-spacing:.15em; padding:14px 20px; border-radius:10px; text-align:center; margin:12px 0; }
  .steps { list-style:none; padding:0; margin:0; }
  .steps li { display:flex; gap:12px; align-items:flex-start; padding:10px 0; border-bottom:1px solid #f1f5f9; font-size:13px; color:#475569; }
  .steps li:last-child { border-bottom:none; }
  .step-num { width:24px; height:24px; min-width:24px; background:#d97706; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; }
  .warning { background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px 16px; font-size:13px; color:#991b1b; margin-bottom:20px; }
  .warning strong { display:block; margin-bottom:4px; }
  .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:24px 32px; text-align:center; }
  .footer p { margin:4px 0; font-size:12px; color:#94a3b8; }
  .footer a { color:#d97706; text-decoration:none; }
  @media(max-width:480px){
    .body { padding:20px; }
    .hero { padding:28px 20px; }
    .amount-big { font-size:22px; }
  }
</style>
</head>
<body>
<div class="wrap">

  {{-- HEADER --}}
  <div class="hero">
    <div class="hero-logo">Resort Pro</div>
    <h1>Đặt phòng thành công! 🎉</h1>
    <p>Chúng tôi đã nhận được đơn đặt phòng của bạn.</p>
    <span class="badge">✓ Đã xác nhận thanh toán</span>
  </div>

  <div class="body">

    {{-- GREETING --}}
    <p class="greeting">
      Xin chào <strong>{{ $order->customer->full_name }}</strong>,<br>
      Đơn đặt phòng của bạn đã được xác nhận. Dưới đây là thông tin chi tiết:
    </p>

    {{-- MÃ ĐẶT PHÒNG --}}
    <p class="section-title">Mã đặt phòng</p>
    <div class="code-box">{{ $order->transfer_code }}</div>
    <p style="font-size:12px;color:#94a3b8;text-align:center;margin:0 0 24px;">Lưu mã này để xuất trình khi check-in</p>

    {{-- THÔNG TIN PHÒNG --}}
    <p class="section-title">Thông tin phòng</p>
    <div class="card">
      <div class="row">
        <span class="row-label">Phòng số</span>
        <span class="row-value">{{ $order->room->room_number }}</span>
      </div>
      <div class="row">
        <span class="row-label">Loại phòng</span>
        <span class="row-value" style="text-transform:capitalize;">{{ $order->room->type }}</span>
      </div>
      <div class="row">
        <span class="row-label">Khu vực</span>
        <span class="row-value">{{ $order->room->zone->name ?? '—' }}</span>
      </div>
      <div class="row">
        <span class="row-label">Nhận phòng</span>
        <span class="row-value">{{ \Carbon\Carbon::parse($order->check_in)->format('d/m/Y') }} · từ 14:00</span>
      </div>
      <div class="row">
        <span class="row-label">Trả phòng</span>
        <span class="row-value">{{ \Carbon\Carbon::parse($order->check_out)->format('d/m/Y') }} · trước 12:00</span>
      </div>
      <div class="row">
        <span class="row-label">Số đêm</span>
        <span class="row-value">
          {{ \Carbon\Carbon::parse($order->check_in)->diffInDays(\Carbon\Carbon::parse($order->check_out)) }} đêm
        </span>
      </div>
      <div class="row">
        <span class="row-label">Số khách</span>
        <span class="row-value">{{ $order->total_guests }} người</span>
      </div>
    </div>

    {{-- DỊCH VỤ ĐÃ CHỌN --}}
    @if($services->isNotEmpty())
    <p class="section-title">Dịch vụ đã chọn</p>
    <div class="card">
      @foreach($services as $svc)
      <div class="row">
        <span class="row-label">{{ $svc->name }} × {{ $svc->quantity }}</span>
        <span class="row-value">{{ number_format($svc->price_at_time * $svc->quantity) }}₫</span>
      </div>
      @endforeach
    </div>
    @endif

    {{-- THANH TOÁN --}}
    <p class="section-title">Thông tin thanh toán</p>
    <div class="highlight-card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div>
          <div class="amount-big">{{ number_format($order->deposit_amount) }}₫</div>
          <div class="amount-label">Đã đặt cọc 30% — Thanh toán phần còn lại khi check-in</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:12px;color:#92400e;">Tổng giá trị đơn</div>
          <div style="font-size:18px;font-weight:700;color:#1e293b;">{{ number_format($order->total_price) }}₫</div>
        </div>
      </div>
      <div style="font-size:12px;color:#92400e;background:#fef3c7;border-radius:8px;padding:10px 12px;">
        💳 Số tiền còn lại <strong>{{ number_format($order->total_price - $order->deposit_amount) }}₫</strong> 
        sẽ được thanh toán khi nhận phòng.
      </div>
    </div>

    {{-- GHI CHÚ --}}
    @if($order->note)
    <p class="section-title">Ghi chú của bạn</p>
    <div class="card" style="font-size:14px;color:#475569;font-style:italic;">
      "{{ $order->note }}"
    </div>
    @endif

    {{-- HƯỚNG DẪN CHECK-IN --}}
    <p class="section-title">Hướng dẫn check-in</p>
    <ul class="steps">
      <li>
        <span class="step-num">1</span>
        <span>Đến quầy lễ tân Resort Pro, xuất trình mã đặt phòng <strong>{{ $order->transfer_code }}</strong> hoặc email này.</span>
      </li>
      <li>
        <span class="step-num">2</span>
        <span>Cung cấp CMND/CCCD để xác minh danh tính.</span>
      </li>
      <li>
        <span class="step-num">3</span>
        <span>Thanh toán phần còn lại <strong>{{ number_format($order->total_price - $order->deposit_amount) }}₫</strong> và nhận chìa khóa phòng.</span>
      </li>
    </ul>

    {{-- CHÍNH SÁCH HỦY --}}
    <div class="warning" style="margin-top:20px;">
      <strong>⚠️ Lưu ý chính sách hủy phòng</strong>
      Đặt cọc 30% sẽ không được hoàn nếu hủy trong vòng 48 giờ trước ngày nhận phòng. 
      Để hủy hoặc thay đổi đặt phòng, vui lòng liên hệ hotline trước thời hạn trên.
    </div>

  </div>{{-- end .body --}}

  {{-- FOOTER --}}
  <div class="footer">
    <p><strong style="color:#1e293b;">Resort Pro</strong></p>
    <p>Nha Trang, Vietnam · <a href="tel:0123456789">0123 456 789</a></p>
    <p style="margin-top:8px;">Email này được gửi tự động, vui lòng không phản hồi trực tiếp.</p>
    <p>Cần hỗ trợ? Liên hệ <a href="mailto:contact@resortpro.com">contact@resortpro.com</a></p>
  </div>

</div>
</body>
</html>