<?php
return [
    'bank_code'        => env('PAYMENT_BANK_CODE', 'TCB'),
    'account_no'       => env('PAYMENT_ACCOUNT_NO', ''),
    'account_name'     => env('PAYMENT_ACCOUNT_NAME', 'RESORT PRO'),
    'hold_minutes'     => env('PAYMENT_HOLD_MINUTES', 15),
    'deposit_rate'     => env('PAYMENT_DEPOSIT_RATE', 0.3),

    'momo_partner_code'=> env('MOMO_PARTNER_CODE', 'MOMO'),
    'momo_access_key'  => env('MOMO_ACCESS_KEY', 'F8BBA842ECF85'),
    'momo_secret_key'  => env('MOMO_SECRET_KEY', 'K951B6PE1waDMi640xX08PD3vg6EkVlz'),
    'momo_endpoint'    => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
    'momo_ipn_url'     => env('MOMO_IPN_URL', ''),
];