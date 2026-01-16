<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Identity verification fields
            $table->string('photo')->nullable()->after('remember_token');
            $table->enum('id_type', ['nin', 'voters_card', 'drivers_license', 'intl_passport'])->nullable()->after('photo');
            $table->string('id_front')->nullable()->after('id_type');
            $table->string('id_back')->nullable()->after('id_front');
            $table->string('id_number')->nullable()->after('id_back');
            $table->date('date_of_birth')->nullable()->after('id_number');
            $table->string('legal_name')->nullable()->after('date_of_birth');
            $table->string('nationality')->nullable()->after('legal_name');
            $table->string('state_of_origin')->nullable()->after('nationality');

            // Verification status and tokens
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified')->after('state_of_origin');
            $table->string('verification_token', 64)->nullable()->unique()->after('verification_status');
            $table->timestamp('verification_token_expires_at')->nullable()->after('verification_token');
            $table->timestamp('verified_at')->nullable()->after('verification_token_expires_at');
            $table->text('verification_notes')->nullable()->after('verified_at');

            // Enrollment ID (10 char alphanumeric)
            $table->string('enrollment_id', 10)->nullable()->unique()->after('verification_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'id_type',
                'id_front',
                'id_back',
                'id_number',
                'date_of_birth',
                'legal_name',
                'nationality',
                'state_of_origin',
                'verification_status',
                'verification_token',
                'verification_token_expires_at',
                'verified_at',
                'verification_notes',
                'enrollment_id',
            ]);
        });
    }
};
