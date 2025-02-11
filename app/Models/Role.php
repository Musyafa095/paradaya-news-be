<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;


class Role extends Model
{
    use HasFactory, HasUuids;
protected $table = 'roles';
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    public function user()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
