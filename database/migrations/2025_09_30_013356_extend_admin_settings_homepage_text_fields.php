<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Split into multiple smaller alter statements to avoid row size limits
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_hero_headline')) $table->text('hp_hero_headline')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_hero_sub')) $table->text('hp_hero_sub')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_trusted_by')) $table->text('hp_trusted_by')->nullable();
            
            if (!Schema::hasColumn('admin_settings', 'hp_access_title')) $table->text('hp_access_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_watch_title')) $table->text('hp_card_watch_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_watch_desc')) $table->text('hp_card_watch_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_creators_title')) $table->text('hp_card_creators_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_creators_desc')) $table->text('hp_card_creators_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_advertisers_title')) $table->text('hp_card_advertisers_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_advertisers_desc')) $table->text('hp_card_advertisers_desc')->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_advertisers_title')) $table->text('hp_advertisers_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_advertisers_sub')) $table->text('hp_advertisers_sub')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_bullet_audience')) $table->text('hp_bullet_audience')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_bullet_story')) $table->text('hp_bullet_story')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_bullet_distribution')) $table->text('hp_bullet_distribution')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_bullet_measurement')) $table->text('hp_bullet_measurement')->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_card_brand_story_title')) $table->text('hp_card_brand_story_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_brand_story_desc')) $table->text('hp_card_brand_story_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_creator_partnerships_title')) $table->text('hp_card_creator_partnerships_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_creator_partnerships_desc')) $table->text('hp_card_creator_partnerships_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_event_coverage_title')) $table->text('hp_card_event_coverage_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_event_coverage_desc')) $table->text('hp_card_event_coverage_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_performance_title')) $table->text('hp_card_performance_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_card_performance_desc')) $table->text('hp_card_performance_desc')->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_sim_title')) $table->text('hp_sim_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_sub')) $table->text('hp_sim_sub')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_followers_q')) $table->text('hp_sim_followers_q')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_price_q')) $table->text('hp_sim_price_q')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_estimated_subs')) $table->text('hp_sim_estimated_subs')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_you_could_earn')) $table->text('hp_sim_you_could_earn')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_note')) $table->text('hp_sim_note')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_sim_disclaimer')) $table->text('hp_sim_disclaimer')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_need_custom_plan')) $table->text('hp_need_custom_plan')->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_campaigns_title')) $table->text('hp_campaigns_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_campaigns_note')) $table->text('hp_campaigns_note')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_case_local_launch')) $table->text('hp_case_local_launch')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_case_local_launch_desc')) $table->text('hp_case_local_launch_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_step_brief_detail')) $table->text('hp_step_brief_detail')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_step_story_detail')) $table->text('hp_step_story_detail')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_step_production_detail')) $table->text('hp_step_production_detail')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_step_distribution_detail')) $table->text('hp_step_distribution_detail')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_step_measurement_detail')) $table->text('hp_step_measurement_detail')->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_oshow_title')) $table->text('hp_oshow_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_oshow_desc')) $table->text('hp_oshow_desc')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_oshow_sponsorship')) $table->text('hp_oshow_sponsorship')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_oshow_deliverables')) $table->text('hp_oshow_deliverables')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_oshow_options')) $table->text('hp_oshow_options')->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_media_shows_label')) $table->string('hp_media_shows_label', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_shows_value')) $table->string('hp_media_shows_value', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_reach_label')) $table->string('hp_media_reach_label', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_reach_value')) $table->string('hp_media_reach_value', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_watch_label')) $table->string('hp_media_watch_label', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_watch_value')) $table->string('hp_media_watch_value', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_partners_label')) $table->string('hp_media_partners_label', 100)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_media_partners_value')) $table->string('hp_media_partners_value', 100)->nullable();
        });
        
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'hp_channel_title')) $table->text('hp_channel_title')->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show1_name')) $table->string('hp_show1_name', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show1_tag')) $table->string('hp_show1_tag', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show2_name')) $table->string('hp_show2_name', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show2_tag')) $table->string('hp_show2_tag', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show3_name')) $table->string('hp_show3_name', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show3_tag')) $table->string('hp_show3_tag', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show4_name')) $table->string('hp_show4_name', 200)->nullable();
            if (!Schema::hasColumn('admin_settings', 'hp_show4_tag')) $table->string('hp_show4_tag', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'hp_hero_headline','hp_hero_sub','hp_trusted_by',
                'hp_access_title','hp_card_watch_title','hp_card_watch_desc','hp_card_creators_title','hp_card_creators_desc','hp_card_advertisers_title','hp_card_advertisers_desc',
                'hp_advertisers_title','hp_advertisers_sub','hp_bullet_audience','hp_bullet_story','hp_bullet_distribution','hp_bullet_measurement',
                'hp_card_brand_story_title','hp_card_brand_story_desc','hp_card_creator_partnerships_title','hp_card_creator_partnerships_desc',
                'hp_card_event_coverage_title','hp_card_event_coverage_desc','hp_card_performance_title','hp_card_performance_desc',
                'hp_sim_title','hp_sim_sub','hp_sim_followers_q','hp_sim_price_q','hp_sim_estimated_subs','hp_sim_you_could_earn','hp_sim_note','hp_sim_disclaimer','hp_need_custom_plan',
                'hp_campaigns_title','hp_campaigns_note','hp_case_local_launch','hp_case_local_launch_desc',
                'hp_step_brief_detail','hp_step_story_detail','hp_step_production_detail','hp_step_distribution_detail','hp_step_measurement_detail',
                'hp_oshow_title','hp_oshow_desc','hp_oshow_sponsorship','hp_oshow_deliverables','hp_oshow_options',
                'hp_media_shows_label','hp_media_shows_value','hp_media_reach_label','hp_media_reach_value','hp_media_watch_label','hp_media_watch_value','hp_media_partners_label','hp_media_partners_value',
                'hp_channel_title','hp_show1_name','hp_show1_tag','hp_show2_name','hp_show2_tag','hp_show3_name','hp_show3_tag','hp_show4_name','hp_show4_tag',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};