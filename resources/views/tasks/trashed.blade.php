@extends('layouts.app')

@section('title', 'Удаленные задачи')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Удаленные задачи (Мягкое удаление)</h2>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Назад к списку</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Категория</th>
                <th>Удалено</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>
                        @if($task->category)
                            <span class="badge bg-info">{{ $task->category->name }}</span>
                        @endif
                    </td>
                    <td>{{ $task->deleted_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <form action="{{ route('tasks.restore', $task->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Восстановить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Нет удаленных задач
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $tasks->links() }}
</div>
@endsection
