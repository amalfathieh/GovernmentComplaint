<?php

namespace App\Providers;

use App\Repositories\Complaint\ComplaintService;
use App\Repositories\Complaint\ComplaintServiceInterface;
use App\Repositories\Complaint\TransactionalComplaintService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ComplaintServiceInterface::class, function () {
            return new TransactionalComplaintService(new ComplaintService());
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
