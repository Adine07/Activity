<?php

namespace Database\Seeders;

use App\Models\Timesheet;
use Illuminate\Database\Seeder;

class TimesheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timesheets = [
            [
                'project_id' => 1,
                'user_id' => 1,
                'date' => '2026-04-19',
                'title' => 'Project 1',
                'description' => 'Description 1',
            ],
            [
                'project_id' => 1,
                'user_id' => 2,
                'date' => '2026-04-19',
                'title' => 'Project 1',
                'description' => 'Description 1',
            ],
            [
                'project_id' => 1,
                'user_id' => 3,
                'date' => '2026-04-19',
                'title' => 'Project 1',
                'description' => 'Description 1',
            ],
            [
                'project_id' => 1,
                'user_id' => 4,
                'date' => '2026-04-19',
                'title' => 'Project 1',
                'description' => 'Description 1',
            ],
            [
                'project_id' => 2,
                'user_id' => 1,
                'date' => '2026-04-19',
                'title' => 'Project 2',
                'description' => 'Description 2',
            ],
            [
                'project_id' => 2,
                'user_id' => 2,
                'date' => '2026-04-19',
                'title' => 'Project 2',
                'description' => 'Description 2',
            ],
            [
                'project_id' => 2,
                'user_id' => 3,
                'date' => '2026-04-19',
                'title' => 'Project 2',
                'description' => 'Description 2',
            ],
            [
                'project_id' => 2,
                'user_id' => 4,
                'date' => '2026-04-19',
                'title' => 'Project 2',
                'description' => 'Description 2',
            ],
            [
                'project_id' => 3,
                'user_id' => 1,
                'date' => '2026-04-19',
                'title' => 'Project 3',
                'description' => 'Description 3',
            ],
            [
                'project_id' => 3,
                'user_id' => 2,
                'date' => '2026-04-19',
                'title' => 'Project 3',
                'description' => 'Description 3',
            ],
            [
                'project_id' => 3,
                'user_id' => 3,
                'date' => '2026-04-19',
                'title' => 'Project 3',
                'description' => 'Description 3',
            ],
            [
                'project_id' => 3,
                'user_id' => 4,
                'date' => '2026-04-19',
                'title' => 'Project 3',
                'description' => 'Description 3',
            ],
        ];

        foreach ($timesheets as $timesheet) {
            Timesheet::create($timesheet);
        }
    }
}
