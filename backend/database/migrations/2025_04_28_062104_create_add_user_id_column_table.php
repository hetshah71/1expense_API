<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('groups', 'user_id')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('expenses', 'user_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'user_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('groups', 'user_id')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
