<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Старые роуты из первого задания (форма с сохранением в JSON)
Route::get('/form', [FormController::class, 'showForm'])->name('form.show');
Route::post('/form', [FormController::class, 'submitForm'])->name('form.submit');
Route::get('/submissions', [FormController::class, 'listSubmissions'])->name('submissions.list');

// Новые роуты для работы с БД

// Tasks - полный CRUD
Route::resource('tasks', TaskController::class);
Route::get('tasks-trashed', [TaskController::class, 'trashed'])->name('tasks.trashed');
Route::post('tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
Route::post('tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');

// Categories - полный CRUD
Route::resource('categories', CategoryController::class);
Route::get('categories-trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');

// Tags - управление
Route::resource('tags', TagController::class)->except(['show']);