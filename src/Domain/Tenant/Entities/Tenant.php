<?php

declare(strict_types=1);

namespace Domain\Tenant\Entities;

use Domain\Tenant\Factories\TenantFactory;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
