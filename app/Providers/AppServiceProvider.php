<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
    }
}
