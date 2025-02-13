<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'categories';

    protected $fillable = ['name'];

    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }
}

