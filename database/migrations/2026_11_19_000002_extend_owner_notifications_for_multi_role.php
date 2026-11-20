<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('owner_notifications')) {
            return;
        }

        Schema::table('owner_notifications', function (Blueprint $table): void {
            if (! Schema::hasColumn('owner_notifications', 'target_role')) {
                $table->string('target_role', 24)->default('owner')->after('id');
            }
            if (! Schema::hasColumn('owner_notifications', 'target_user_id')) {
                $table->unsignedBigInteger('target_user_id')->nullable()->after('target_role');
            }

            $table->index(['target_role', 'read_at', 'occurred_at'], 'notif_role_read_occurred_idx');
            $table->index(['target_role', 'target_user_id', 'read_at'], 'notif_role_user_read_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('owner_notifications')) {
            return;
        }

        Schema::table('owner_notifications', function (Blueprint $table): void {
            $table->dropIndex('notif_role_read_occurred_idx');
            $table->dropIndex('notif_role_user_read_idx');

            if (Schema::hasColumn('owner_notifications', 'target_user_id')) {
                $table->dropColumn('target_user_id');
            }
            if (Schema::hasColumn('owner_notifications', 'target_role')) {
                $table->dropColumn('target_role');
            }
        });
    }
};
