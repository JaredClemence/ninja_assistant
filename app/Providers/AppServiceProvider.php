<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
            $exception = $event->exception;
            /* @var $exception Exception */
            $file=$exception->getFile();
            $line=$exception->getLine();
            $trace=$exception->getTraceAsString();
            $message=$exception->getMessage();
            $formattedError = "Error thrown on line $line in $file with message:\n\t$message\n\nTrace:\n$trace";
            \Illuminate\Support\Facades\Log::error($formattedError);
            echo $formattedError;
        });
        Paginator::useBootstrapThree();
    }
}
