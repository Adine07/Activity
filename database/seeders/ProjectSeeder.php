<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Project 1',
                'description' => 'Description 1',
                'git_repo' => 'https://github.com/project-1',
                'live_url' => 'https://project-1.com',
                'is_active' => true,
            ],
            [
                'name' => 'Project 2',
                'description' => 'Description 2',
                'git_repo' => 'https://github.com/project-2',
                'live_url' => 'https://project-2.com',
                'is_active' => true,
            ],
            [
                'name' => 'Project 3',
                'description' => 'Description 3',
                'git_repo' => 'https://github.com/project-3',
                'live_url' => 'https://project-3.com',
                'is_active' => true,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
