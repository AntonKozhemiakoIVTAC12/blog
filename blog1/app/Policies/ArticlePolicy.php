<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\Group;
use App\Models\User;

class ArticlePolicy
{
    public function view(User $user, Article $article)
    {
        return $user->groups->contains($article->group_id);
    }

    public function update(User $user, Article $article)
    {
        return $user->groups->contains($article->group_id);
    }

    public function create(User $user)
    {
        return $user->activeGroup() !== null;
    }

    public function delete(User $user, Article $article)
    {
        return $this->update($user, $article);
    }

    public function manageGroup(User $user, Group $group)
    {
        return $user->id === $group->admin_id;
    }
}
