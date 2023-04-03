<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        app()->bind("countryList",function($app){
             
            $data =json_decode(getAllCountries(),true); 
            return $data;
        });

        app()->bind("phoneCodes",function($app){
             
            $data =json_decode(getPhoneCode(),true); 
            return $data;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
         Paginator::useBootstrap();
    }
}
