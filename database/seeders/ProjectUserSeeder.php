<?php

namespace Database\Seeders;

use App\Models\ProjectUser;
use Illuminate\Database\Seeder;

class ProjectUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectUsers = [
            [
                'project_id' => 1,
                'user_id' => 1,
            ],
            [
                'project_id' => 1,
                'user_id' => 2,
            ],
            [
                'project_id' => 1,
                'user_id' => 3,
            ],
            [
                'project_id' => 1,
                'user_id' => 4,
            ],
            [
                'project_id' => 2,
                'user_id' => 1,
            ],
            [
                'project_id' => 2,
                'user_id' => 2,
            ],
            [
                'project_id' => 2,
                'user_id' => 3,
            ],
            [
                'project_id' => 2,
                'user_id' => 4,
            ],
            [
                'project_id' => 3,
                'user_id' => 1,
            ],
            [
                'project_id' => 3,
                'user_id' => 2,
            ],
            [
                'project_id' => 3,
                'user_id' => 3,
            ],
            [
                'project_id' => 3,
                'user_id' => 4,
            ],
        ];

        foreach ($projectUsers as $projectUser) {
            ProjectUser::create($projectUser);
        }
    }
}
