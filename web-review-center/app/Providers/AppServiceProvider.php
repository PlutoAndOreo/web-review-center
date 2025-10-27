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
            $user = auth()->user();
            $userName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Profile';
            $profileUrl = $user ? "/users/{$user->id}/edit" : "#";
            $logoutUrl = $user ? '/admin/logout' : '#';

            $event->menu->add([
                'text'   => "Hi! $userName",
                'icon'   => 'fas fa-user',
                'topnav_right' => 'right',
                'icon_color' => 'primary',
                'icon_right' => true,
                'submenu' => [
                    [
                        'text' => 'Profile',
                        'url'  => $profileUrl,
                        'icon' => 'fas fa-user',
                    ],
                    [
                        'text'   => 'Logout',
                        'url'    => '#logout',
                        'icon'   => 'fas fa-sign-out-alt',
                    ],
                ],
            ]);
    
        });
    }
}
