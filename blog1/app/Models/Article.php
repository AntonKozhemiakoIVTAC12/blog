<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ArticleFactory;
class Article extends Model
{
    use HasFactory;
    protected $casts = [
        'gost_data' => 'array', 'components' => 'array'
    ];

    protected $fillable = [
        'title', 'gost_data', 'user_id', 'standard', 'components', 'group_id'
    ];

    protected $factory = ArticleFactory::class;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeSearch($query, $searchQuery)
    {
        if ($searchQuery) {
            return $query->where('title', 'like', '%' . $searchQuery . '%');
        }

        return $query;
    }
}
