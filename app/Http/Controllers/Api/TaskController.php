<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Список всех задач с фильтрами и связями
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['category', 'tags', 'comments']);

        // Применение Query Scopes
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->boolean('completed')) {
            $query->completed();
        }

        if ($request->boolean('active')) {
            $query->active();
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $tasks = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'total' => $tasks->total(),
                'per_page' => $tasks->perPage(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
            ]
        ]);
    }

    /**
     * Создание новой задачи
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task = Task::create($validated);

        // Привязка тегов (BelongsToMany)
        if (isset($validated['tags'])) {
            $task->tags()->attach($validated['tags']);
        }

        $task->load(['category', 'tags']);

        return response()->json([
            'success' => true,
            'message' => 'Задача успешно создана',
            'data' => new TaskResource($task)
        ], 201);
    }

    /**
     * Просмотр конкретной задачи
     */
    public function show(Task $task): JsonResponse
    {
        $task->load(['category', 'tags', 'comments']);

        return response()->json([
            'success' => true,
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Обновление задачи
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task->update($validated);

        // Синхронизация тегов
        if (isset($validated['tags'])) {
            $task->tags()->sync($validated['tags']);
        }

        $task->load(['category', 'tags']);

        return response()->json([
            'success' => true,
            'message' => 'Задача успешно обновлена',
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Мягкое удаление задачи
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete(); // Мягкое удаление

        return response()->json([
            'success' => true,
            'message' => 'Задача успешно удалена'
        ]);
    }

    /**
     * Восстановление удаленной задачи
     */
    public function restore($id): JsonResponse
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return response()->json([
            'success' => true,
            'message' => 'Задача успешно восстановлена',
            'data' => new TaskResource($task->load(['category', 'tags']))
        ]);
    }

    /**
     * Получение удаленных задач
     */
    public function trashed(): JsonResponse
    {
        $tasks = Task::onlyTrashed()
            ->with(['category', 'tags'])
            ->latest('deleted_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'total' => $tasks->total(),
                'per_page' => $tasks->perPage(),
                'current_page' => $tasks->currentPage(),
            ]
        ]);
    }

    /**
     * Добавление комментария к задаче (полиморфная связь)
     */
    public function addComment(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $task->comments()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Комментарий добавлен',
            'data' => $comment
        ], 201);
    }
}