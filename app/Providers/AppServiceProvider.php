<?php

namespace App\Providers;

use App\Mail\MailjetApiTransport;
use App\Models\SupplyRequestItem;
use App\Policies\RequestItemPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrap();
        Gate::policy(SupplyRequestItem::class, RequestItemPolicy::class);

        Mail::extend('mailjet-api', function () {
            $apiKey = config('mail.mailers.mailjet-api.key');
            $secret = config('mail.mailers.mailjet-api.secret');

            if (!$apiKey || !$secret) {
                throw new \RuntimeException(
                    'Mailjet API credentials are not configured. Set MAILJET_API_KEY and MAILJET_SECRET_KEY in .env'
                );
            }

            return new MailjetApiTransport($apiKey, $secret);
        });
    }
}

