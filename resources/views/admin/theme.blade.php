@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.theme') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
                </div>
              @endif

              @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

           <form method="post" action="{{{ url('panel/admin/theme') }}}" enctype="multipart/form-data">
             @csrf
             <div class="mb-4">
               <ul class="nav nav-pills" id="themeTabs">
                 <li class="nav-item"><a class="nav-link active" data-target="#pane-general" href="#">General</a></li>
                 <li class="nav-item"><a class="nav-link" data-target="#pane-homepage" href="#">Homepage</a></li>
               </ul>
             </div>
             <div id="pane-general">

						 <fieldset class="row mb-5">
			         <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('general.home_style') }}</legend>
			         <div class="col-sm-10">
			           <div class="form-check mb-3">
			             <input class="form-check-input" type="radio" name="home_style" id="radio1" @checked($settings->home_style == 0) value="0">
			             <label class="form-check-label" for="radio1">
			               <img class="border" src="{{ asset('img/homepage-1.jpg') }}">
			             </label>
			           </div>
			           <div class="form-check mb-3">
			             <input class="form-check-input" type="radio" name="home_style" id="radio2" @checked($settings->home_style == 1) value="1">
			             <label class="form-check-label" for="radio2">
							<img class="border" src="{{ asset('img/homepage-2.jpg') }}">
			             </label>
			           </div>

					   <div class="form-check">
						<input class="form-check-input" type="radio" name="home_style" id="radio3" @checked($settings->home_style == 2) value="2">
						<label class="form-check-label" for="radio3">
						   <img class="border" src="{{ asset('img/homepage-explore.jpg') }}">
						</label>
					  </div>
			         </div>
			       </fieldset><!-- end row -->

             </div><!-- /pane-general -->
             <div id="pane-homepage" class="d-none">
             <hr class="my-4">
             <h6 class="mb-3 fw-medium">Homepage (Odeka)</h6>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Hero Type</label>
                <div class="col-sm-10">
                  <select name="hero_type" class="form-select">
                    <option value="image" @selected($settings->hero_type=='image')>Image</option>
                    <option value="youtube" @selected($settings->hero_type=='youtube')>YouTube</option>
                  </select>
            		</div>
            </div><!-- /pane-homepage -->
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Hero Image Source</label>
                <div class="col-sm-10">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <select name="hero_image_source" class="form-select">
                        <option value="upload" @selected($settings->hero_image_source=='upload')>Upload</option>
                        <option value="url" @selected($settings->hero_image_source=='url')>External URL</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="hero_image_url" value="{{ $settings->hero_image_url }}" class="form-control" placeholder="https://... (optional)">
                    </div>
                  </div>
                  <div class="input-group mt-2">
                    <input name="hero_image_file" type="file" class="form-control custom-file rounded-pill">
                  </div>
                  <small class="d-block">Recommended: 4:3, ≥1280×960 (JPG/PNG/WEBP)</small>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Hero YouTube URL</label>
                <div class="col-sm-10">
                  <input type="text" name="hero_youtube_url" value="{{ $settings->hero_youtube_url }}" class="form-control" placeholder="https://youtube.com/watch?v=...">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">O'Show Media Type</label>
                <div class="col-sm-10">
                  <select name="oshow_media_type" class="form-select">
                    <option value="image" @selected($settings->oshow_media_type=='image')>Image</option>
                    <option value="youtube" @selected($settings->oshow_media_type=='youtube')>YouTube</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">O'Show Image</label>
                <div class="col-sm-10">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <select name="oshow_image_source" class="form-select">
                        <option value="upload" @selected($settings->oshow_image_source=='upload')>Upload</option>
                        <option value="url" @selected($settings->oshow_image_source=='url')>External URL</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="oshow_image_url" value="{{ $settings->oshow_image_url }}" class="form-control" placeholder="https://... (optional)">
                    </div>
                  </div>
                  <div class="input-group mt-2">
                    <input name="oshow_image_file" type="file" class="form-control custom-file rounded-pill">
                  </div>
                  <small class="d-block">Recommended: 16:10, ≥1600×1000 (JPG/PNG/WEBP)</small>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">O'Show YouTube URL</label>
                <div class="col-sm-10">
                  <input type="text" name="oshow_youtube_url" value="{{ $settings->oshow_youtube_url }}" class="form-control" placeholder="https://youtube.com/watch?v=...">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Latest Episode URL</label>
                <div class="col-sm-10">
                  <input type="text" name="oshow_latest_watch_url" value="{{ $settings->oshow_latest_watch_url }}" class="form-control" placeholder="https://...">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Sponsorship Kit (PDF)</label>
                <div class="col-sm-10">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <select name="oshow_sponsorship_pdf_source" class="form-select">
                        <option value="upload" @selected($settings->oshow_sponsorship_pdf_source=='upload')>Upload</option>
                        <option value="url" @selected($settings->oshow_sponsorship_pdf_source=='url')>External URL</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="oshow_sponsorship_pdf_url" value="{{ $settings->oshow_sponsorship_pdf_url }}" class="form-control" placeholder="https://... (optional)">
                    </div>
                  </div>
                  <div class="input-group mt-2">
                    <input name="oshow_sponsorship_pdf_file" type="file" class="form-control custom-file rounded-pill" accept="application/pdf">
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Media Kit (PDF)</label>
                <div class="col-sm-10">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <select name="media_kit_source" class="form-select">
                        <option value="upload" @selected($settings->media_kit_source=='upload')>Upload</option>
                        <option value="url" @selected($settings->media_kit_source=='url')>External URL</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="media_kit_url" value="{{ $settings->media_kit_url }}" class="form-control" placeholder="https://... (optional)">
                    </div>
                  </div>
                  <div class="input-group mt-2">
                    <input name="media_kit_file" type="file" class="form-control custom-file rounded-pill" accept="application/pdf">
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Case Study (PDF)</label>
                <div class="col-sm-10">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <select name="case_study_source" class="form-select">
                        <option value="upload" @selected($settings->case_study_source=='upload')>Upload</option>
                        <option value="url" @selected($settings->case_study_source=='url')>External URL</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="case_study_url" value="{{ $settings->case_study_url }}" class="form-control" placeholder="https://... (optional)">
                    </div>
                  </div>
                  <div class="input-group mt-2">
                    <input name="case_study_file" type="file" class="form-control custom-file rounded-pill" accept="application/pdf">
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Simulator Defaults</label>
                <div class="col-sm-10">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <input type="number" step="0.001" min="0" max="1" name="sim_default_conversion" value="{{ $settings->sim_default_conversion }}" class="form-control" placeholder="Conversion (e.g. 0.05)">
                    </div>
                    <div class="col-md-4">
                      <input type="number" step="0.001" min="0" max="1" name="sim_platform_fee" value="{{ $settings->sim_platform_fee }}" class="form-control" placeholder="Platform fee (e.g. 0.05)">
                    </div>
                  </div>
                  <small class="d-block mt-2">Optional per-currency ranges JSON: {"XOF":{"min":500,"max":20000,"step":100}}</small>
                  <textarea name="sim_price_ranges_json" rows="3" class="form-control mt-2">{{ $settings->sim_price_ranges_json }}</textarea>
                </div>
              </div>

              <hr class="my-4">
              <h6 class="mb-3 fw-medium">Homepage Text Content</h6>
              <p class="text-muted small">Leave fields empty to use default translations from lang/en/odeka.php</p>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Hero Section</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_hero_headline" value="{{ $settings->hp_hero_headline ?: trans('odeka.hero_headline', [], 'en') }}" class="form-control mb-2">
                  <textarea name="hp_hero_sub" rows="2" class="form-control mb-2">{{ $settings->hp_hero_sub ?: trans('odeka.hero_sub', [], 'en') }}</textarea>
                  <input type="text" name="hp_trusted_by" value="{{ $settings->hp_trusted_by ?: trans('odeka.trusted_by', [], 'en') }}" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Access Platform Cards</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_access_title" value="{{ $settings->hp_access_title ?: trans('odeka.access_platform', [], 'en') }}" class="form-control mb-2">
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_card_watch_title" value="{{ $settings->hp_card_watch_title ?: trans('odeka.card_watch', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_watch_desc" rows="1" class="form-control">{{ $settings->hp_card_watch_desc ?: trans('odeka.card_watch_desc', [], 'en') }}</textarea></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_card_creators_title" value="{{ $settings->hp_card_creators_title ?: trans('odeka.card_creators', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_creators_desc" rows="1" class="form-control">{{ $settings->hp_card_creators_desc ?: trans('odeka.card_creators_desc', [], 'en') }}</textarea></div>
                  </div>
                  <div class="row g-2">
                    <div class="col-md-6"><input type="text" name="hp_card_advertisers_title" value="{{ $settings->hp_card_advertisers_title ?: trans('odeka.card_advertisers', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_advertisers_desc" rows="1" class="form-control">{{ $settings->hp_card_advertisers_desc ?: trans('odeka.card_advertisers_desc', [], 'en') }}</textarea></div>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Advertisers (Odeka Tab)</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_advertisers_title" value="{{ $settings->hp_advertisers_title ?: trans('odeka.advertisers_title', [], 'en') }}" class="form-control mb-2">
                  <textarea name="hp_advertisers_sub" rows="2" class="form-control mb-2">{{ $settings->hp_advertisers_sub ?: trans('odeka.advertisers_sub', [], 'en') }}</textarea>
                  <input type="text" name="hp_bullet_audience" value="{{ $settings->hp_bullet_audience ?: trans('odeka.bullet_audience', [], 'en') }}" class="form-control mb-1">
                  <input type="text" name="hp_bullet_story" value="{{ $settings->hp_bullet_story ?: trans('odeka.bullet_story', [], 'en') }}" class="form-control mb-1">
                  <input type="text" name="hp_bullet_distribution" value="{{ $settings->hp_bullet_distribution ?: trans('odeka.bullet_distribution', [], 'en') }}" class="form-control mb-1">
                  <input type="text" name="hp_bullet_measurement" value="{{ $settings->hp_bullet_measurement ?: trans('odeka.bullet_measurement', [], 'en') }}" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Service Cards (4)</label>
                <div class="col-sm-10">
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_card_brand_story_title" value="{{ $settings->hp_card_brand_story_title ?: trans('odeka.brand_story', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_brand_story_desc" rows="1" class="form-control">{{ $settings->hp_card_brand_story_desc ?: 'Short‑form narratives produced by Odeka Studio — from teaser to hero film.' }}</textarea></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_card_creator_partnerships_title" value="{{ $settings->hp_card_creator_partnerships_title ?: trans('odeka.creator_partnerships', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_creator_partnerships_desc" rows="1" class="form-control">{{ $settings->hp_card_creator_partnerships_desc ?: 'Tap trusted local voices to extend reach and authenticity.' }}</textarea></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_card_event_coverage_title" value="{{ $settings->hp_card_event_coverage_title ?: trans('odeka.event_coverage', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_event_coverage_desc" rows="1" class="form-control">{{ $settings->hp_card_event_coverage_desc ?: 'On‑site capture + same‑day edits for festivals and launches.' }}</textarea></div>
                  </div>
                  <div class="row g-2">
                    <div class="col-md-6"><input type="text" name="hp_card_performance_title" value="{{ $settings->hp_card_performance_title ?: trans('odeka.performance_addons', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><textarea name="hp_card_performance_desc" rows="1" class="form-control">{{ $settings->hp_card_performance_desc ?: 'Retargeting, UTM tracking, A/B hooks, caption optimization.' }}</textarea></div>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Simulator Section</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_sim_title" value="{{ $settings->hp_sim_title ?: trans('odeka.sim_title', [], 'en') }}" class="form-control mb-2">
                  <input type="text" name="hp_sim_sub" value="{{ $settings->hp_sim_sub ?: trans('odeka.sim_sub', [], 'en') }}" class="form-control mb-2">
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_sim_followers_q" value="{{ $settings->hp_sim_followers_q ?: trans('odeka.sim_followers_q', [], 'en') }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_sim_price_q" value="{{ $settings->hp_sim_price_q ?: trans('odeka.sim_price_q', [], 'en') }}" class="form-control"></div>
                  </div>
                  <input type="text" name="hp_sim_estimated_subs" value="{{ $settings->hp_sim_estimated_subs ?: trans('odeka.sim_estimated_subs', [], 'en') }}" class="form-control mb-2">
                  <input type="text" name="hp_sim_you_could_earn" value="{{ $settings->hp_sim_you_could_earn ?: trans('odeka.sim_you_could_earn', [], 'en') }}" class="form-control mb-2">
                  <textarea name="hp_sim_note" rows="2" class="form-control mb-2">{{ $settings->hp_sim_note ?: 'Conversion assumed: 5% of followers subscribe. Platform fee: 5% deducted. Payment processor fees not included.' }}</textarea>
                  <textarea name="hp_sim_disclaimer" rows="2" class="form-control mb-2">{{ $settings->hp_sim_disclaimer ?: '* Estimate only. Based on 5% of followers who subscribe. Does not include payment processor fees. Net amount reflects a 5% platform fee.' }}</textarea>
                  <input type="text" name="hp_need_custom_plan" value="{{ $settings->hp_need_custom_plan ?: __('odeka.need_custom_plan') }}" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Campaigns Section</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_campaigns_title" value="{{ $settings->hp_campaigns_title ?: trans('odeka.campaigns_title', [], 'en') }}" class="form-control mb-2">
                  <input type="text" name="hp_campaigns_note" value="{{ $settings->hp_campaigns_note ?: trans('odeka.campaigns_note', [], 'en') }}" class="form-control mb-2">
                  <input type="text" name="hp_case_local_launch" value="{{ $settings->hp_case_local_launch ?: trans('odeka.case_local_launch', [], 'en') }}" class="form-control mb-2">
                  <textarea name="hp_case_local_launch_desc" rows="2" class="form-control">{{ $settings->hp_case_local_launch_desc ?: '4‑video story arc, creator collaborations, and paid boosts. Outcome example: +38% visits in 4 weeks, +12% repeat.' }}</textarea>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Campaign Steps (5)</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_step_brief_detail" value="{{ $settings->hp_step_brief_detail ?: 'Objectives, audience, budget, target markets.' }}" class="form-control mb-1">
                  <input type="text" name="hp_step_story_detail" value="{{ $settings->hp_step_story_detail ?: 'Creative routes, scripts, casting, visual language.' }}" class="form-control mb-1">
                  <input type="text" name="hp_step_production_detail" value="{{ $settings->hp_step_production_detail ?: 'Studio or on‑location. Photo + video + design.' }}" class="form-control mb-1">
                  <input type="text" name="hp_step_distribution_detail" value="{{ $settings->hp_step_distribution_detail ?: "O'Channel + creators + paid amplification." }}" class="form-control mb-1">
                  <input type="text" name="hp_step_measurement_detail" value="{{ $settings->hp_step_measurement_detail ?: 'Analytics and brand lift study.' }}" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">O'Show Section</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_oshow_title" value="{{ $settings->hp_oshow_title ?: trans('odeka.oshow_star', [], 'en') }}" class="form-control mb-2">
                  <textarea name="hp_oshow_desc" rows="2" class="form-control mb-2">{{ $settings->hp_oshow_desc ?: trans('odeka.oshow_desc', [], 'en') }}</textarea>
                  <input type="text" name="hp_oshow_sponsorship" value="{{ $settings->hp_oshow_sponsorship ?: trans('odeka.oshow_sponsorship', [], 'en') }}" class="form-control mb-1">
                  <input type="text" name="hp_oshow_deliverables" value="{{ $settings->hp_oshow_deliverables ?: trans('odeka.oshow_deliverables', [], 'en') }}" class="form-control mb-1">
                  <input type="text" name="hp_oshow_options" value="{{ $settings->hp_oshow_options ?: trans('odeka.oshow_options', [], 'en') }}" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">Media Tab Stats</label>
                <div class="col-sm-10">
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_media_shows_label" value="{{ $settings->hp_media_shows_label ?: 'Shows' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_media_shows_value" value="{{ $settings->hp_media_shows_value ?: '6+' }}" class="form-control"></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_media_reach_label" value="{{ $settings->hp_media_reach_label ?: 'Monthly reach' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_media_reach_value" value="{{ $settings->hp_media_reach_value ?: '500K+' }}" class="form-control"></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_media_watch_label" value="{{ $settings->hp_media_watch_label ?: 'Avg. watch time' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_media_watch_value" value="{{ $settings->hp_media_watch_value ?: '3m 12s' }}" class="form-control"></div>
                  </div>
                  <div class="row g-2">
                    <div class="col-md-6"><input type="text" name="hp_media_partners_label" value="{{ $settings->hp_media_partners_label ?: 'Partners' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_media_partners_value" value="{{ $settings->hp_media_partners_value ?: '40+' }}" class="form-control"></div>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-lg-end">O'Channel Shows</label>
                <div class="col-sm-10">
                  <input type="text" name="hp_channel_title" value="{{ $settings->hp_channel_title ?: "O'Channel — Emissions" }}" class="form-control mb-2">
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_show1_name" value="{{ $settings->hp_show1_name ?: "O'Show (flagship)" }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_show1_tag" value="{{ $settings->hp_show1_tag ?: 'Interviews • Music • Culture' }}" class="form-control"></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_show2_name" value="{{ $settings->hp_show2_name ?: 'Street Stories' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_show2_tag" value="{{ $settings->hp_show2_tag ?: 'People • Places • Food' }}" class="form-control"></div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-md-6"><input type="text" name="hp_show3_name" value="{{ $settings->hp_show3_name ?: 'Creator Spotlight' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_show3_tag" value="{{ $settings->hp_show3_tag ?: 'Models • Creators • Makers' }}" class="form-control"></div>
                  </div>
                  <div class="row g-2">
                    <div class="col-md-6"><input type="text" name="hp_show4_name" value="{{ $settings->hp_show4_name ?: 'Business Now' }}" class="form-control"></div>
                    <div class="col-md-6"><input type="text" name="hp_show4_tag" value="{{ $settings->hp_show4_tag ?: 'Entrepreneurs • Playbooks' }}" class="form-control"></div>
                  </div>
                </div>
              </div>

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.logo') }}</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->logo) }}" class="bg-secondary" style="width:150px">
                </div>

                <div class="input-group mb-1">
                  <input name="logo" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 487x144 px (PNG, SVG)</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.logo_blue') }}</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->logo_2) }}" style="width:150px">
                </div>

                <div class="input-group mb-1">
                  <input name="logo_2" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 487x144 px (PNG, SVG)</small>
		          </div>
		        </div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-label text-lg-end">{{ __('general.watermak_video') }}</label>
					<div class="col-lg-5 col-sm-10">
				  <div class="d-block mb-2">
					<img src="{{ asset('img/'.$settings->watermak_video) }}" class="bg-dark" style="width:150px">
				  </div>
  
				  <div class="input-group mb-1">
					<input name="watermak_video" type="file" class="form-control custom-file rounded-pill">
				  </div>
				  <small class="d-block">{{ __('general.recommended_size') }} 487x144 px (PNG)</small>
					</div>
				  </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Favicon</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->favicon) }}">
                </div>

                <div class="input-group mb-1">
                  <input name="favicon" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 48x48 px (PNG)</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.index_image_top') }}</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->home_index) }}" style="width:200px">
                </div>

                <div class="input-group mb-1">
                  <input name="index_image_top" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 884x592 px</small>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.background') }}</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img class="img-fluid" src="{{ asset('img/'.$settings->bg_gradient) }}" style="width:400px">
                </div>

                <div class="input-group mb-1">
                  <input name="background" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 1441x480 px</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">O'Show (flagship) image</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->img_1) }}" style="width:120px">
                </div>

                <div class="input-group mb-1">
                  <input name="image_index_1" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 120x120 px</small>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Street Stories image</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->img_2) }}" style="width:120px">
                </div>

                <div class="input-group mb-1">
                  <input name="image_index_2" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 120x120 px</small>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Creator Spotlight image</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->img_3) }}" style="width:120px">
                </div>

                <div class="input-group mb-1">
                  <input name="image_index_3" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 120x120 px</small>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Business Now image</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ asset('img/'.$settings->img_4) }}" style="width:120px">
                </div>

                <div class="input-group mb-1">
                  <input name="image_index_4" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 362x433 px</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.avatar_default') }}</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <img src="{{ Helper::getFile(config('path.avatar').$settings->avatar) }}" style="width:200px">
                </div>

                <div class="input-group mb-1">
                  <input name="avatar" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 250x250 px</small>
		          </div>
		        </div>

            <div class="row mb-4">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.cover_default') }}</label>
		          <div class="col-lg-5 col-sm-10">
                <div class="d-block mb-2">
                  <div style="max-width: 400px; height: 150px; margin-bottom: 10px; display: block; border-radius: 6px; background: #505050 @if ($settings->cover_default) url('{{ Helper::getFile(config('path.cover').$settings->cover_default) }}') no-repeat center center; background-size: cover; @endif ;">
                </div>

                <div class="input-group mb-1">
                  <input name="cover_default" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block">{{ __('general.recommended_size') }} 1500x800 px</small>
		          </div>
		        </div>
						</div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.default_color') }}</label>
		          <div class="col-sm-10">
                <input type="color" name="color" class="form-control form-control-color" value="{{ $settings->color_default }}">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.navbar_background_color') }}</label>
		          <div class="col-sm-10">
                <input type="color" name="navbar_background_color" class="form-control form-control-color" value="{{ $settings->navbar_background_color }}">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.navbar_text_color') }}</label>
		          <div class="col-sm-10">
                <input type="color" name="navbar_text_color" class="form-control form-control-color" value="{{ $settings->navbar_text_color }}">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.footer_background_color') }}</label>
		          <div class="col-sm-10">
                <input type="color" name="footer_background_color" class="form-control form-control-color" value="{{ $settings->footer_background_color }}">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('general.footer_text_color') }}</label>
		          <div class="col-sm-10">
                <input type="color" name="footer_text_color" class="form-control form-control-color" value="{{ $settings->footer_text_color }}">
		          </div>
		        </div>

						<fieldset class="row mb-3">
							<legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('general.button_style') }}</legend>
							<div class="col-sm-10">
								<div class="form-check">
									<input class="form-check-input" type="radio" name="button_style" id="button_style1" @if ($settings->button_style == 'rounded') checked="checked" @endif value="rounded" checked>
									<label class="form-check-label" for="button_style1">
										{{ trans('general.button_style_rounded') }}
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="button_style" id="button_style2" @if ($settings->button_style == 'normal') checked="checked" @endif value="normal">
									<label class="form-check-label" for="button_style2">
										{{ trans('admin.normal') }}
									</label>
								</div>
							</div>
						</fieldset><!-- end row -->

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
		          </div>
		        </div>

           </form>
           <script>
             (function(){
               const tabs = document.querySelectorAll('#themeTabs .nav-link');
               function activate(id){
                 document.querySelector('#pane-general').classList.toggle('d-none', id !== '#pane-general');
                 document.querySelector('#pane-homepage').classList.toggle('d-none', id !== '#pane-homepage');
                 tabs.forEach(t=>t.classList.toggle('active', t.getAttribute('data-target') === id));
               }
               tabs.forEach(t=>t.addEventListener('click', function(e){ e.preventDefault(); activate(this.getAttribute('data-target')); }));
             })();
           </script>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
