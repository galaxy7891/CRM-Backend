<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::create([
            'id' => '654fg817-e89b-6641-a456-98734500',
            'title' => 'CRM Keren',
            'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Alias, veniam.',
            'status' => 'draft',
            'post_date' => null,
        ]);
        
        Article::create([
            'id' => '654fg817-e89b-6641-a456-65734501',
            'title' => 'LC Keren',
            'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Alias, veniam.',
            'status' => 'post',
            'post_date' => now(),
        ]);
    }
}
