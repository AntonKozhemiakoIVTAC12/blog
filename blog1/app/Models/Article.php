<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Database\Factories\ArticleFactory;
class Article extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'content', 'user_id'];
    protected $factory = ArticleFactory::class;
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
