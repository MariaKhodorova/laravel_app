<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Список всех задач
     */
    public function index(Request $request)
    {
        $query = Task::with(['category', 'tags']);

        // Фильтрация через Query Scopes
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $tasks = $query->latest()->paginate(10);
        $categories = Category::all();
        
        return view('tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Форма создания задачи
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        
        return view('tasks.create', compact('categories', 'tags'));
    }

    /**
     * Сохранение новой задачи
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task = Task::create($validated);

        // Привязка тегов (BelongsToMany связь)
        if (isset($validated['tags'])) {
            $task->tags()->attach($validated['tags']);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Задача успешно создана!');
    }

    /**
     * Просмотр задачи
     */
    public function show(Task $task)
    {
        $task->load(['category', 'tags', 'comments']);
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Форма редактирования задачи
     */
    public function edit(Task $task)
    {
        $categories = Category::all();
        $tags = Tag::all();
        
        return view('tasks.edit', compact('task', 'categories', 'tags'));
    }

    /**
     * Обновление задачи
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task->update($validated);

        // Синхронизация тегов
        if (isset($validated['tags'])) {
            $task->tags()->sync($validated['tags']);
        } else {
            $task->tags()->detach();
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Задача успешно обновлена!');
    }

    /**
     * Мягкое удаление задачи
     */
    public function destroy(Task $task)
    {
        $task->delete(); // Мягкое удаление благодаря SoftDeletes

        return redirect()->route('tasks.index')
            ->with('success', 'Задача удалена!');
    }

    /**
     * Список удаленных задач
     */
    public function trashed()
    {
        $tasks = Task::onlyTrashed()
            ->with(['category', 'tags'])
            ->latest('deleted_at')
            ->paginate(10);
        
        return view('tasks.trashed', compact('tasks'));
    }

    /**
     * Восстановление задачи
     */
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return redirect()->route('tasks.index')
            ->with('success', 'Задача восстановлена!');
    }

    /**
     * Добавление комментария (полиморфная связь)
     */
    public function addComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $task->comments()->create($validated);

        return back()->with('success', 'Комментарий добавлен!');
    }
}