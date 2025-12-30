<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создание категорий
        $categories = [
            ['name' => 'Работа', 'description' => 'Рабочие задачи'],
            ['name' => 'Личное', 'description' => 'Личные дела'],
            ['name' => 'Проекты', 'description' => 'Проектные задачи'],
            ['name' => 'Учеба', 'description' => 'Образовательные задачи'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Создание тегов
        $tags = [
            ['name' => 'Срочно', 'color' => '#EF4444'],
            ['name' => 'Важно', 'color' => '#F59E0B'],
            ['name' => 'Планирование', 'color' => '#3B82F6'],
            ['name' => 'Разработка', 'color' => '#10B981'],
            ['name' => 'Документация', 'color' => '#6366F1'],
            ['name' => 'Тестирование', 'color' => '#8B5CF6'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }

        // Создание задач
        $allCategories = Category::all();
        $allTags = Tag::all();

        $tasks = [
            [
                'title' => 'Завершить проект Laravel',
                'description' => 'Доработать все функции и протестировать',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Написать документацию API',
                'description' => 'Описать все endpoints и примеры использования',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Провести код-ревью',
                'description' => 'Проверить pull requests команды',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Изучить новый фреймворк',
                'description' => 'React или Vue.js для frontend',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => now()->addDays(14),
            ],
            [
                'title' => 'Оплатить счета',
                'description' => 'Коммунальные услуги за месяц',
                'status' => 'completed',
                'priority' => 'medium',
                'due_date' => now()->subDays(2),
            ],
        ];

        foreach ($tasks as $index => $taskData) {
            $task = Task::create(array_merge($taskData, [
                'category_id' => $allCategories->random()->id
            ]));

            // Привязка случайных тегов (BelongsToMany)
            $task->tags()->attach(
                $allTags->random(rand(1, 3))->pluck('id')
            );

            // Добавление комментариев (полиморфная связь)
            $task->comments()->create([
                'content' => 'Это тестовый комментарий к задаче #' . ($index + 1)
            ]);

            if (rand(0, 1)) {
                $task->comments()->create([
                    'content' => 'Еще один комментарий для примера работы полиморфных связей'
                ]);
            }
        }

        // Добавление комментариев к категориям (демонстрация полиморфизма)
        foreach ($allCategories->take(2) as $category) {
            $category->comments()->create([
                'content' => 'Комментарий к категории: ' . $category->name
            ]);
        }

        $this->command->info('База данных заполнена тестовыми данными!');
        $this->command->info('Создано категорий: ' . Category::count());
        $this->command->info('Создано тегов: ' . Tag::count());
        $this->command->info('Создано задач: ' . Task::count());
        $this->command->info('Создано комментариев: ' . Comment::count());
    }
}