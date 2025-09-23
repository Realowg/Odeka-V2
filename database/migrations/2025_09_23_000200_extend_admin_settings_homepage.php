<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_settings')) {
            return; // safety for older installs
        }

        Schema::table('admin_settings', function (Blueprint $table) {
            // Hero
            if (! Schema::hasColumn('admin_settings', 'hero_type')) $table->string('hero_type', 20)->nullable();
            if (! Schema::hasColumn('admin_settings', 'hero_image_source')) $table->string('hero_image_source', 10)->nullable();
            if (! Schema::hasColumn('admin_settings', 'hero_image_url')) $table->text('hero_image_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'hero_image_file')) $table->string('hero_image_file', 255)->nullable();
            if (! Schema::hasColumn('admin_settings', 'hero_youtube_url')) $table->text('hero_youtube_url')->nullable();

            // Channel shows JSON
            if (! Schema::hasColumn('admin_settings', 'channel_shows_json')) $table->longText('channel_shows_json')->nullable();

            // O'Show
            if (! Schema::hasColumn('admin_settings', 'oshow_media_type')) $table->string('oshow_media_type', 20)->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_image_source')) $table->string('oshow_image_source', 10)->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_image_url')) $table->text('oshow_image_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_image_file')) $table->string('oshow_image_file', 255)->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_youtube_url')) $table->text('oshow_youtube_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_latest_watch_url')) $table->text('oshow_latest_watch_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_sponsorship_pdf_source')) $table->string('oshow_sponsorship_pdf_source', 10)->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_sponsorship_pdf_url')) $table->text('oshow_sponsorship_pdf_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'oshow_sponsorship_pdf_file')) $table->string('oshow_sponsorship_pdf_file', 255)->nullable();

            // PDFs
            if (! Schema::hasColumn('admin_settings', 'media_kit_source')) $table->string('media_kit_source', 10)->nullable();
            if (! Schema::hasColumn('admin_settings', 'media_kit_url')) $table->text('media_kit_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'media_kit_file')) $table->string('media_kit_file', 255)->nullable();
            if (! Schema::hasColumn('admin_settings', 'case_study_source')) $table->string('case_study_source', 10)->nullable();
            if (! Schema::hasColumn('admin_settings', 'case_study_url')) $table->text('case_study_url')->nullable();
            if (! Schema::hasColumn('admin_settings', 'case_study_file')) $table->string('case_study_file', 255)->nullable();

            // Simulator
            if (! Schema::hasColumn('admin_settings', 'sim_default_conversion')) $table->decimal('sim_default_conversion', 5, 4)->nullable();
            if (! Schema::hasColumn('admin_settings', 'sim_platform_fee')) $table->decimal('sim_platform_fee', 5, 4)->nullable();
            if (! Schema::hasColumn('admin_settings', 'sim_price_ranges_json')) $table->longText('sim_price_ranges_json')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_settings')) {
            return;
        }
        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'hero_type','hero_image_source','hero_image_url','hero_image_file','hero_youtube_url',
                'channel_shows_json',
                'oshow_media_type','oshow_image_source','oshow_image_url','oshow_image_file','oshow_youtube_url','oshow_latest_watch_url','oshow_sponsorship_pdf_source','oshow_sponsorship_pdf_url','oshow_sponsorship_pdf_file',
                'media_kit_source','media_kit_url','media_kit_file','case_study_source','case_study_url','case_study_file',
                'sim_default_conversion','sim_platform_fee','sim_price_ranges_json',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('admin_settings', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};


