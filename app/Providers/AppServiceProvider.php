<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * FIX: Symfony Mailer SSL certificate verify failed trên Windows/Laragon
         *
         * config/mail.php 'stream' => ['ssl' => ...] chỉ hoạt động với SwiftMailer (Laravel 8).
         * Laravel 9+ dùng Symfony Mailer → phải override transport trực tiếp như bên dưới.
         *
         * Xóa / comment block này khi deploy lên production (server có SSL cert đúng).
         */
        Mail::extend('smtp', function (array $config) {
            $isSsl = isset($config['encryption'])
                && strtolower($config['encryption']) === 'ssl';

            $transport = new EsmtpTransport(
                $config['host'] ?? 'smtp.gmail.com',
                (int) ($config['port'] ?? 587),
                $isSsl
            );

            if (!empty($config['username'])) {
                $transport->setUsername($config['username']);
            }
            if (!empty($config['password'])) {
                $transport->setPassword($config['password']);
            }
            if (!empty($config['timeout'])) {
                $transport->setRestartThreshold(
                    100,
                    (int) $config['timeout']
                );
            }

            // Tắt verify SSL — chỉ dùng trên local/dev
            $transport->getStream()->setStreamOptions([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ]);

            return $transport;
        });
    }
}