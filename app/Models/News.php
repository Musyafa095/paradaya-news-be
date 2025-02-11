<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;


class News extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'news';
    protected $fillable = ['title', 'content', 'image' , 'category_id', 'year'];

   public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function comment()
    {
        return $this->hasMany(Comment::class, 'news_id');
    }

}