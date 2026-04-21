<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'git_repo', 'live_url', 'is_active', 'created_by'])]
class Project extends Model
{
    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
