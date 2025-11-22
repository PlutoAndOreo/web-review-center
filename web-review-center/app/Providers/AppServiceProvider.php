<?php

namespace App\Providers;

use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

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

        // Share admin profile URL with adminlte views
        View::composer('adminlte::page', function ($view) {
            $user = auth()->guard('admin')->user();
            if ($user) {
                $profileUrl = url('/admin/users/' . $user->id . '/edit');
                $view->with('adminProfileUrl', $profileUrl);
            }
        });

        app('events')->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $user = auth()->user();
            $userName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Profile';
            $profileUrl = $user ? "/admin/users/{$user->id}/edit" : "#";
            $logoutUrl = $user ? '/admin/logout' : '#';

            // Get unread notification count for admin
            $unreadCount = 0;
            if ($user && auth()->guard('admin')->check()) {
                $unreadCount = Notification::where('admin_id', $user->id)
                    ->where('is_read', false)
                    ->count();
            }

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
