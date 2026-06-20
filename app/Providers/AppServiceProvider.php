<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\View;

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
        View::composer(
        'layouts.admin',
        function($view){


            if(auth()->check()){


                $notifications = auth()
                ->user()
                ->notifications()
                ->latest()
                ->take(5)
                ->get();



                $unreadNotifications = auth()
                ->user()
                ->unreadNotifications()
                ->count();



                $view->with([

                    'notifications'=>$notifications,

                    'unreadNotifications'=>$unreadNotifications

                ]);


            }


        }
    );

    }
}
