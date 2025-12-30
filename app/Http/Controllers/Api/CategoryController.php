<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Список всех категорий
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::withCount('tasks');

        // Применение Query Scopes
        if ($request->boolean('with_tasks')) {
            $query->withTasks();
        }

        if ($request->boolean('popular')) {
            $query->popular($request->get('min_tasks', 5));
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * Создание новой категории
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно создана',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Просмотр конкретной категории с задачами
     */
    public function show(Category $category): JsonResponse
    {
        $category->load(['tasks.tags', 'comments']);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Обновление категории
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно обновлена',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Мягкое удаление категории
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно удалена'
        ]);
    }

    /**
     * Восстановление удаленной категории
     */
    public function restore($id): JsonResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно восстановлена',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Получение задач категории
     */
    public function tasks(Category $category): JsonResponse
    {
        $tasks = $category->tasks()
            ->with(['tags', 'comments'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
}