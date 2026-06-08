<?php

namespace App\Providers;

use App\Models\SupplyRequestItem;
use App\Policies\RequestItemPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrap();
        Gate::policy(SupplyRequestItem::class, RequestItemPolicy::class);
    }
}
