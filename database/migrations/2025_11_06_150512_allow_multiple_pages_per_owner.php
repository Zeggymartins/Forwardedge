<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'pageable_id') && Schema::hasColumn('pages', 'pageable_type')) {
                $table->dropUnique('pages_pageable_id_pageable_type_unique');
                $table->index(['pageable_id', 'pageable_type'], 'pages_pageable_lookup_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'pageable_id') && Schema::hasColumn('pages', 'pageable_type')) {
                $table->dropIndex('pages_pageable_lookup_index');
                $table->unique(['pageable_id', 'pageable_type'], 'pages_pageable_id_pageable_type_unique');
            }
        });
    }
};
