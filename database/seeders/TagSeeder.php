<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::create(['name' => 'Bug', 'color' => '#FF0000']);
        Tag::create(['name' => 'Feature', 'color' => '#00FF00']);
        Tag::create(['name' => 'Improvement', 'color' => '#0000FF']);
    }
}