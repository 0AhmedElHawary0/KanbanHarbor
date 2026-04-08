<?php

declare(strict_types=1);

namespace Domain\Task\Entities;

use Domain\Sprint\Entities\Sprint;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Task extends Model
{
    use HasFactory;


    protected $fillable = [
        'tenant_id',
        'project_id',
        'sprint_id',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'story_points',
        'assignee_id',
        'due_date',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id');
    }
}
