<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'comments';
    protected $fillable = ['comment', 'user_id', 'news_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
}

