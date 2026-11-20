<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('owner_notifications')) {
            return;
        }

        Schema::create('owner_notifications', function (Blueprint $table): void {
            $table->id();
            $table->string('event_key')->unique();
            $table->string('type', 32);
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['read_at', 'occurred_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_notifications');
    }
};
