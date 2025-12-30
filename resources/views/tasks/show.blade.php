@extends('layouts.app')

@section('title', 'Просмотр задачи')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3>{{ $task->title }}</h3>
        <div>
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">Редактировать</a>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>
    <div class="card-body">
        <p><strong>Описание:</strong> {{ $task->description ?? 'Нет описания' }}</p>
        <p><strong>Статус:</strong> 
            <span class="badge bg-primary">{{ $task->status }}</span>
        </p>
        <p><strong>Приоритет:</strong> 
            <span class="badge bg-warning">{{ $task->priority }}</span>
        </p>
        <p><strong>Срок:</strong> {{ $task->due_date ? $task->due_date->format('d.m.Y') : '-' }}</p>
        
        @if($task->category)
            <p><strong>Категория (BelongsTo):</strong> 
                <span class="badge bg-info">{{ $task->category->name }}</span>
            </p>
        @endif

        @if($task->tags->count() > 0)
            <p><strong>Теги (BelongsToMany):</strong>
                @foreach($task->tags as $tag)
                    <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                @endforeach
            </p>
        @endif

        <hr>

        <h4>Комментарии (Полиморфная связь MorphMany)</h4>
        @forelse($task->comments as $comment)
            <div class="alert alert-light">
                {{ $comment->content }}
                <small class="text-muted d-block">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
            </div>
        @empty
            <p class="text-muted">Нет комментариев</p>
        @endforelse

        <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="mt-3">
            @csrf
            <div class="mb-3">
                <label class="form-label">Добавить комментарий</label>
                <textarea name="content" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
    </div>
</div>
@endsection