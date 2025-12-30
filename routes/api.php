<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // Tasks API
    Route::apiResource('tasks', TaskController::class);
    Route::get('tasks-trashed', [TaskController::class, 'trashed']);
    Route::post('tasks/{id}/restore', [TaskController::class, 'restore']);
    Route::post('tasks/{task}/comments', [TaskController::class, 'addComment']);

    // Categories API
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/{category}/tasks', [CategoryController::class, 'tasks']);
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore']);

    // Tags API
    Route::apiResource('tags', TagController::class);
    Route::get('tags/{tag}/tasks', [TagController::class, 'tasks']);

    // Comments API
    Route::apiResource('comments', CommentController::class)->only(['index', 'destroy']);
    Route::post('comments/{id}/restore', [CommentController::class, 'restore']);
    
    // Statistics
    Route::get('stats', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'total_tasks' => \App\Models\Task::count(),
                'completed_tasks' => \App\Models\Task::completed()->count(),
                'active_tasks' => \App\Models\Task::active()->count(),
                'overdue_tasks' => \App\Models\Task::overdue()->count(),
                'total_categories' => \App\Models\Category::count(),
                'total_tags' => \App\Models\Tag::count(),
            ]
        ]);
    });
});