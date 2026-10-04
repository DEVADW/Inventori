<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        //
        Builder::macro('search', function (array $columns, $term) {
            return $this->where(function ($query) use ($term, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'LIKE', "%{$term}%");
                }
            });
        });


        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('isGL', function (User $user) {
            return $user->jabatan === 'GL';
        });

         Gate::define('isStaff', function (User $user) {
            return $user->jabatan === 'staff';
        });
         Gate::define('is_non_staff', function (User $user) {
            return $user->jabatan === 'non_staff';
        });
        Gate::define('isIct', function (User $user) {
            return in_array(strtolower(trim((string) $user->jabatan)), [
                'ict',
                'hardware_enggineer',
                'hardware engineer',
                'hardware engg',
                'hardware_engg',
                'ict_technician',
                'ict technician',
                'non_staff',
                 'helper',
            ], true);
        });
         Gate::define('isHelper', function (User $user) {
            return $user->jabatan === 'helper';
        });
         Gate::define('BukuTamu', function (User $user) {
            return in_array($user->jabatan, ['admin', 'security']);
        });

        Gate::define('createBtn', function (User $user) {
            return in_array($user->jabatan, ['admin', 'staff', 'non_staff']);
        });
    }
}
