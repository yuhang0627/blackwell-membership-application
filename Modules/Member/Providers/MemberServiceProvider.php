<?php

namespace Modules\Member\Providers;

use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MemberServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Explicit route model binding so {member} resolves correctly from module routes
        Route::model('member', Member::class);

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'member');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register(): void
    {
        //
    }
}
