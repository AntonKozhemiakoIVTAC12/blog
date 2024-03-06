<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;
use Faker\Generator as Faker;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $title = $this->faker->sentence;

        return [
            'title' => $title,
            'slug' => str::slug($title), // Использование Laravel Str helper для генерации slug
            'content' => $this->faker->paragraphs(3, true), // Генерация трех параграфов контента
            'user_id' => $this->faker->numberBetween(1, 100) // Генерация случайного числа в пределах пользовательских ID
        ];
    }
}
