<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('documents')) {
            return;
        }

        if (!Schema::hasColumn('documents', 'type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('type')->nullable()->after('name')->index();
            });
        }

        // Backfill existing installs where documents were provider-only.
        DB::table('documents')->whereNull('type')->update(['type' => 'provider_document']);
    }

    public function down(): void
    {
        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropIndex(['type']);
                $table->dropColumn('type');
            });
        }
    }
};

