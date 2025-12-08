<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('scholarship_applications', 'score')) {
                $table->integer('score')->nullable()->after('form_data');
            }
            if (!Schema::hasColumn('scholarship_applications', 'auto_decision')) {
                $table->string('auto_decision')->nullable()->after('score');
            }
            if (!Schema::hasColumn('scholarship_applications', 'decision_notes')) {
                $table->text('decision_notes')->nullable()->after('auto_decision');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            if (Schema::hasColumn('scholarship_applications', 'decision_notes')) {
                $table->dropColumn('decision_notes');
            }
            if (Schema::hasColumn('scholarship_applications', 'auto_decision')) {
                $table->dropColumn('auto_decision');
            }
            if (Schema::hasColumn('scholarship_applications', 'score')) {
                $table->dropColumn('score');
            }
        });
    }
};
