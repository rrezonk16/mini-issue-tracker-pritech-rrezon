<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Issue;
use App\Models\Tag;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            TagSeeder::class,
        ]);

        // Create 1 main user to own all projects
        $owner = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);


        // Create 5 projects, with the main user as the owner
        Project::factory(5)->create([
            'owner_id' => $owner->id,
            'deadline' => now()->addDays(rand(10, 30))->format('Y-m-d'), // Ensure date format
        ])->each(function ($project) use ($tags, $users, $owner) {
            // Create 3-6 issues per project
            Issue::factory(rand(3, 6))->for($project)->create([
                'status' => fake()->randomElement(['open', 'in_progress', 'closed']),
                'priority' => fake()->randomElement(['low', 'medium', 'high']),
                'due_date' => now()->addDays(rand(5, 15))->format('Y-m-d'), // Ensure date format
            ])->each(function ($issue) use ($tags, $users, $owner) {
                // Assign 1-3 tags to each issue
                $issue->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());

                // Assign 1-3 users to each issue (including the owner)
                $randomUsers = $users->random(rand(1, 3))->pluck('id')->toArray();
                if (!in_array($owner->id, $randomUsers)) {
                    $randomUsers[] = $owner->id;
                }
                $issue->users()->attach($randomUsers, ['created_at' => now(), 'updated_at' => now()]);

                // Create 1-4 comments per issue
                Comment::factory(rand(1, 4))->for($issue)->create();
            });
        });
    }
}
