<?php

use Domain\Sprint\Enums\SprintStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name',120);
            $table->text('goal')->nullable();
            $table->enum('status', SprintStatus::values())->default(SprintStatus::Active->value);
            $table->date('starts_at')->default(DB::raw('CURRENT_DATE'));
            $table->date('ends_at')->default(DB::raw('(CURRENT_DATE + 14)'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
