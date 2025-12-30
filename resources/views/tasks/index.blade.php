@extends('layouts.app')

@section('title', 'Список задач')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Список задач</h2>
    <div>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Создать задачу</a>
        <a href="{{ route('tasks.trashed') }}" class="btn btn-secondary">Удаленные</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Фильтры с использованием Query Scopes -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Поиск..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Все статусы</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>В работе</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select">
                    <option value="">Все приоритеты</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Низкий</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Средний</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Высокий</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Фильтровать</button>
            </div>
        </form>
    </div>
</div>

<!-- Список задач -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Категория (BelongsTo)</th>
                <th>Теги (BelongsToMany)</th>
                <th>Статус</th>
                <th>Приоритет</th>
                <th>Срок</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>
                    </td>
                    <td>
                        @if($task->category)
                            <span class="badge bg-info">{{ $task->category->name }}</span>
                        @else
                            <span class="text-muted">Нет категории</span>
                        @endif
                    </td>
                    <td>
                        @foreach($task->tags as $tag)
                            <span class="badge" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'warning',
                                'in_progress' => 'primary',
                                'completed' => 'success'
                            ];
                            $statusLabels = [
                                'pending' => 'Ожидает',
                                'in_progress' => 'В работе',
                                'completed' => 'Завершено'
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusClasses[$task->status] }}">
                            {{ $statusLabels[$task->status] }}
                        </span>
                    </td>
                    <td>
                        @php
                            $priorityClasses = [
                                'low' => 'secondary',
                                'medium' => 'warning',
                                'high' => 'danger'
                            ];
                            $priorityLabels = [
                                'low' => 'Низкий',
                                'medium' => 'Средний',
                                'high' => 'Высокий'
                            ];
                        @endphp
                        <span class="badge bg-{{ $priorityClasses[$task->priority] }}">
                            {{ $priorityLabels[$task->priority] }}
                        </span>
                    </td>
                    <td>{{ $task->due_date ? $task->due_date->format('d.m.Y') : '-' }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info">Просмотр</a>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">Изменить</a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить задачу?')">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        Задачи не найдены
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Пагинация -->
<div class="d-flex justify-content-center">
    {{ $tasks->links() }}
</div>
@endsection