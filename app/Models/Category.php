<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Автоматическое создание slug при сохранении
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Связь: Категория имеет много задач (HasMany)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Полиморфная связь: Категория может иметь комментарии
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Query Scope: Категории с задачами
    public function scopeWithTasks($query)
    {
        return $query->has('tasks');
    }

    // Query Scope: Популярные категории (с количеством задач больше указанного)
    public function scopePopular($query, $minTasks = 5)
    {
        return $query->withCount('tasks')
            ->having('tasks_count', '>=', $minTasks);
    }

    // Query Scope: Поиск по имени
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }
}