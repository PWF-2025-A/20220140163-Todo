<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Routing\Route;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // tambahkan ini
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        Gate::define('admin',function ($user){
            return $user ->is_admin == true;
        });
        Scramble::configure()->routes (function (Route $route) {
            return Str::startsWith($route->uri, 'api/');
        })->withDocumentTransformers( function(OpenApi $openApi){
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
        // Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
