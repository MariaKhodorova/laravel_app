<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'category_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'medium',
    ];

    // Связь: Задача принадлежит категории (BelongsTo)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Связь: Задача имеет много тегов (BelongsToMany)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withTimestamps();
    }

    // Полиморфная связь: Задача может иметь комментарии
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Query Scope: Завершенные задачи
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Query Scope: Активные задачи (не завершенные)
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    // Query Scope: Задачи с высоким приоритетом
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    // Query Scope: Просроченные задачи
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    // Query Scope: Задачи по статусу
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Query Scope: Задачи по приоритету
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Query Scope: Поиск по названию или описанию
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    // Query Scope: Задачи с тегами
    public function scopeWithTags($query)
    {
        return $query->has('tags');
    }
}