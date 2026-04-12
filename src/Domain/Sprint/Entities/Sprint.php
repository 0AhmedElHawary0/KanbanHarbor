<?php

declare(strict_types=1);

namespace Domain\Sprint\Entities;

use Domain\Project\Entities\Project;
use Domain\Sprint\Enums\SprintStatus;
use Domain\Sprint\Factories\SprintFactory;
use Domain\Task\Entities\Task;
use Domain\Tenant\Entities\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'goal',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SprintStatus::class,
        ];
    }

    protected static function newFactory(): SprintFactory
    {
        return SprintFactory::new();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
