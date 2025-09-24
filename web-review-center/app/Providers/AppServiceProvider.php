<?php

namespace App\Providers;

use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\ServiceProvider;
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

        View::composer('*', function ($view) {
            $view->with('userName', auth()->user()->name ?? 'Guest');
        });

        app('events')->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add([
                'text'   => '',
                'url'    => '',
                'topnav' => 'right',
                'icon'   => 'fa fa-solid fa-bell',
            ]);
    
            $event->menu->add([
                'text'   => "Hi! ". auth()->user()->first_name . ' ' .auth()->user()->last_name?? 'Profile', // 👈 dynamic variable
                'url'    => '/profile',
                'topnav'     => 'left',
                'icon_color' => 'primary',
                'icon_right' => true, 
            ]);
        });
    }
}
