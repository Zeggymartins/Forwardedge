<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE users MODIFY id_type ENUM('nin','national_id','voters_card','drivers_license','intl_passport','student_id','work_id') NULL"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE users MODIFY id_type ENUM('nin','voters_card','drivers_license','intl_passport') NULL"
        );
    }
};
