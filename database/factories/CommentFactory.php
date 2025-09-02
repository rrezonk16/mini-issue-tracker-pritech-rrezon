<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Issue;

class CommentFactory extends Factory
{
    protected $model = \App\Models\Comment::class;

    public function definition()
    {
        return [
            'issue_id' => Issue::factory(),
            'author_name' => $this->faker->name(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
