<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'git_repo', 'live_url', 'is_active'])]
class Project extends Model
{
    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
}
