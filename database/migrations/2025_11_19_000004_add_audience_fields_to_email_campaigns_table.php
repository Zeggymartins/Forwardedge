<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('email_campaigns', 'audience_sources')) {
                $table->json('audience_sources')->nullable()->after('blocks');
            }
            if (!Schema::hasColumn('email_campaigns', 'include_emails')) {
                $table->json('include_emails')->nullable()->after('audience_sources');
            }
            if (!Schema::hasColumn('email_campaigns', 'exclude_emails')) {
                $table->json('exclude_emails')->nullable()->after('include_emails');
            }
            if (!Schema::hasColumn('email_campaigns', 'cta_email_param')) {
                $table->string('cta_email_param')->nullable()->after('cta_link');
            }
        });
    }

    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('email_campaigns', 'audience_sources')) {
                $table->dropColumn('audience_sources');
            }
            if (Schema::hasColumn('email_campaigns', 'include_emails')) {
                $table->dropColumn('include_emails');
            }
            if (Schema::hasColumn('email_campaigns', 'exclude_emails')) {
                $table->dropColumn('exclude_emails');
            }
            if (Schema::hasColumn('email_campaigns', 'cta_email_param')) {
                $table->dropColumn('cta_email_param');
            }
        });
    }
};
