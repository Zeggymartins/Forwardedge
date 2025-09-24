<?php

namespace Database\Seeders;

use App\Models\CourseSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseSchedule::create([
            'course_id'  => 1,
            'start_date' => '2025-10-01',
            'end_date'   => '2025-11-30',
            'location'   => 'Lagos',
            'type'       => 'hybrid',
            'price'      => 200000,
        ]);
    }
 
}
