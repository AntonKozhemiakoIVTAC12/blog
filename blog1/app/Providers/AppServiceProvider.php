<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('manage-group', function (User $user, Group $group) {
            return $user->id === $group->admin_id;
        });

        // Права на управление статьей
        Gate::define('manage-article', function (User $user, Article $article) {
            return $user->id === $article->user_id ||
                ($article->group && $user->id === $article->group->admin_id);
        });

        // Права на вступление в группу
        Gate::define('join-group', function (User $user, Group $group) {
            return !$group->users()->where('user_id', $user->id)->exists();
        });
    }
}
