<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskType;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('sprint_id')->nullable()->constrained('sprints')->nullOnDelete();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->enum('type', TaskType::values());
            $table->enum('priority', TaskPriority::values())->default(TaskPriority::Medium->value);
            $table->enum('status', TaskStatus::values())->default(TaskStatus::ToDo->value);
            $table->unsignedTinyInteger('story_points')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'project_id', 'status']);
            $table->index(['sprint_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
