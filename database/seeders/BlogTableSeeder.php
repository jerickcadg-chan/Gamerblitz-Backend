<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Blog;

class BlogTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $i) {
            $title = $faker->sentence(6);

            Blog::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . $i,
                'content' => $faker->paragraphs(5, true),
                'meta_description' => $faker->sentence(15),
                'meta_keyword' => implode(', ', $faker->words(5)),
                'thumbnail' => $faker->imageUrl(800, 600, 'blog', true, 'Blog'),
                'blog_category_id' => $faker->numberBetween(1, 1),
                'user_id' => 1,
                'status' => $faker->randomElement(['draft', 'published']),
                'published_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }
    }
}
