<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleAdmin extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'content', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
