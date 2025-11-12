<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        // //create user roles
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'doctor']);
        // Role::create(['name' => 'patient']);
        
        // //create user permissions
        // Permission::create(['name' => 'manage_users']);
        // Permission::create(['name' => 'manage_patients']);
        // Permission::create(['name' => 'manage_doctors']);

         VerifyEmail::toMailUsing(function ($notifiable, $url) {
        return (new QueuedVerifyEmailNotification)->toMail($notifiable);
    });
    }
}
