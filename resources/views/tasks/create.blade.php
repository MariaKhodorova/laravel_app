@extends('layouts.app')

@section('title', 'Создать задачу')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Создать новую задачу</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Статус *</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>В работе</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Приоритет *</label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Низкий</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Средний</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Высокий</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Срок выполнения</label>
                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                           value="{{ old('due_date') }}">
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Категория (BelongsTo)</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                    <option value="">-- Без категории --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Теги (BelongsToMany)</label>
                <div class="row">
                    @foreach($tags as $tag)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" 
                                       value="{{ $tag->id }}" id="tag{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tag{{ $tag->id }}">
                                    <span class="badge" style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Создать задачу</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>
@endsection