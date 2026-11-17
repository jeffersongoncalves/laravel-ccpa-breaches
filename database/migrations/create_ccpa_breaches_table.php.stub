<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('ccpa-breaches.table', 'ccpa_breaches'), function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('what_happened')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('discovered_at');
            $table->timestamp('due_at')->nullable()->index();
            $table->json('data_categories')->nullable();
            $table->unsignedInteger('affected_residents')->nullable();
            $table->boolean('encrypted')->default(false);
            $table->boolean('notification_required')->nullable();
            $table->text('assessment')->nullable();
            $table->text('what_we_are_doing')->nullable();
            $table->text('what_you_can_do')->nullable();
            $table->text('law_enforcement_delay')->nullable();
            $table->timestamp('residents_notified_at')->nullable();
            $table->boolean('substitute_notice')->default(false);
            $table->timestamp('attorney_general_notified_at')->nullable();
            $table->string('attorney_general_reference')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('ccpa-breaches.table', 'ccpa_breaches'));
    }
};
