<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cybersecurity
        $cyber = Course::create([
            'title' => 'Introduction to Cybersecurity',
            'slug' => 'introduction-to-cybersecurity',
            'description' => 'Learn the fundamentals of protecting digital systems and information.',
            'thumbnail' => null,
            'status' => 'published',
        ]);


        // 2. Cloud Computing
        $cloud = Course::create([
            'title' => 'Cloud Computing Fundamentals',
            'slug' => 'cloud-computing-fundamentals',
            'description' => 'Understand the basics of cloud services and infrastructure.',
            'thumbnail' => null,
            'status' => 'published',
        ]);

      

        // 3. AI & Machine Learning
        $ai = Course::create([
            'title' => 'AI & Machine Learning Essentials',
            'slug' => 'ai-machine-learning-essentials',
            'description' => 'Explore artificial intelligence and machine learning concepts.',
            'thumbnail' => null,
            'status' => 'draft',
        ]);

      
    }
}