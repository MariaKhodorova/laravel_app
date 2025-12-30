<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'commentable_id',
        'commentable_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Полиморфная связь: Комментарий принадлежит commentable (Task или Category)
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    // Query Scope: Комментарии определенного типа
    public function scopeForType($query, $type)
    {
        return $query->where('commentable_type', $type);
    }

    // Query Scope: Недавние комментарии
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}