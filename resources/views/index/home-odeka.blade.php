@php
  // CRITICAL FIX: Force locale from session IMMEDIATELY
  $sessionLocale = session('locale');
  if ($sessionLocale) {
    app()->setLocale($sessionLocale);
  }
  $currentLocale = app()->getLocale();
  
  // DIAGNOSTIC: Log what's happening
  \Log::info('Homepage locale debug', [
    'session_locale' => $sessionLocale,
    'app_locale' => $currentLocale,
    'test_translation' => __('odeka.hero_headline'),
  ]);
  
  // Helper to get text with DB override or fallback to translation
  $t = function($dbKey, $transKey) {
    $dbVal = config('settings.' . $dbKey);
    if ($dbVal) {
      return $dbVal;
    }
    // CRITICAL: Call __() which will use app()->getLocale()
    // Do NOT cache the translation, let Laravel handle it
    return __($transKey);
  };
@endphp
<!doctype html>
<html lang="{{ str_replace('_','-', $currentLocale) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Odeka Media</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      .tabular-nums { font-variant-numeric: tabular-nums; }
      
      /* Smooth scroll */
      html { scroll-behavior: smooth; }
      
      /* Glass morphism effect */
      .glass {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      /* Gradient text */
      .gradient-text {
        background: linear-gradient(135deg, #ffffff 0%, #a0a0a0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }
      
      /* Animated gradient background */
      @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
      }
      .animated-gradient {
        background: linear-gradient(-45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05), rgba(255,255,255,0.08), rgba(255,255,255,0.12));
        background-size: 400% 400%;
        animation: gradient-shift 15s ease infinite;
      }
      
      /* Hover lift effect */
      .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
      }
      
      /* Glow effect */
      .glow {
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
      }
      .glow:hover {
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
      }
      
      /* Fade in animations */
      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
      }
      
      /* Stagger animation delays */
      .delay-100 { animation-delay: 0.1s; opacity: 0; }
      .delay-200 { animation-delay: 0.2s; opacity: 0; }
      .delay-300 { animation-delay: 0.3s; opacity: 0; }
      .delay-400 { animation-delay: 0.4s; opacity: 0; }
    </style>
  </head>
  <!-- DEBUG: Locale={{ $currentLocale }}, Session={{ session('locale') }}, __test={{ __('odeka.brand') }} -->
  <body class="min-h-screen bg-neutral-950 text-neutral-100 selection:bg-neutral-800 selection:text-white" data-tab="{{ request('tab', 'Odeka') }}" data-locale="{{ str_replace('_','-', $currentLocale) }}" data-currency="{{ \App\Helper::displayCurrencyCode() }}">
    <div class="sticky top-0 z-40 glass backdrop-blur-xl bg-neutral-950/80 border-b border-neutral-800/50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <a href="#top" class="flex items-center gap-3 group">
            @php $logo = config('settings.logo') ? asset('img/'.config('settings.logo')) : null; @endphp
            @if($logo)
              <img src="{{ $logo }}" alt="Odeka logo" class="h-7 w-auto transition-transform group-hover:scale-110" />
            @else
              <div class="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0 shadow-[0_0_30px_-10px_rgba(255,255,255,0.7)] transition-all group-hover:shadow-[0_0_40px_-5px_rgba(255,255,255,0.9)]"></div>
            @endif
            <span class="font-semibold tracking-tight group-hover:text-white transition-colors">{{ __('odeka.brand') }}</span>
          </a>
          <div class="hidden md:flex items-center gap-2 p-1 rounded-full glass border-neutral-800/50" id="header-tabs">
            <button data-tab="Odeka" class="px-4 py-1.5 text-sm rounded-full transition-all hover:bg-white/10">Odeka</button>
            <button data-tab="Media" class="px-4 py-1.5 text-sm rounded-full transition-all hover:bg-white/10">Media</button>
          </div>
          <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="hidden sm:inline-flex rounded-full border border-neutral-800 px-4 py-2 text-sm transition-all hover:border-neutral-600 hover:bg-neutral-800/50">{{ __('odeka.sign_in') }}</a>
            <a href="{{ route('home') }}" class="inline-flex rounded-full bg-white text-neutral-900 px-4 py-2 text-sm font-medium transition-all hover:bg-neutral-100 hover:shadow-lg hover:shadow-white/20">{{ __('odeka.open_app') }}</a>
          </div>
        </div>
      </div>
    </div>

    <section class="relative overflow-hidden">
      <div class="relative">
        <div class="pointer-events-none absolute inset-0 -z-10">
          <div class="absolute left-1/2 top-[-10%] h-[700px] w-[900px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(255,255,255,0.12),transparent_70%)] blur-2xl"></div>
          <div class="absolute right-[-10%] bottom-[-20%] h-[500px] w-[500px] rounded-full bg-[conic-gradient(from_180deg_at_50%_50%,rgba(255,255,255,0.08),transparent)] blur-2xl"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-32">
          <div class="grid items-center gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7 space-y-8">
              <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight gradient-text fade-in-up">
                {{ $t('hp_hero_headline', 'odeka.hero_headline') }}
              </h1>
              <p class="mt-6 max-w-2xl text-lg text-neutral-300 leading-relaxed fade-in-up delay-100">
                {{ $t('hp_hero_sub', 'odeka.hero_sub') }}
              </p>
              <div class="mt-8 flex flex-wrap gap-4 fade-in-up delay-200">
                <a href="{{ url('channel') }}" class="group inline-flex items-center gap-2 rounded-full bg-white text-neutral-900 px-8 py-4 text-sm font-semibold transition-all hover:bg-neutral-100 hover:shadow-2xl hover:shadow-white/20 hover:scale-105">
                  <span>{{ __('odeka.watch_on_channel') }}</span>
                  <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full glass border border-neutral-700 px-8 py-4 text-sm font-medium transition-all hover:border-neutral-500 hover:bg-white/10">
                  {{ __('odeka.creator_sign_in') }}
                </a>
                <a href="{{ url('brief') }}" class="inline-flex items-center gap-2 rounded-full glass border border-neutral-700 px-8 py-4 text-sm font-medium transition-all hover:border-neutral-500 hover:bg-white/10">
                  {{ __('odeka.start_campaign') }}
                </a>
              </div>
              <p class="mt-6 text-sm text-neutral-500 fade-in-up delay-300">{{ $t('hp_trusted_by', 'odeka.trusted_by') }}</p>
            </div>
            <div class="lg:col-span-5 fade-in-up delay-300">
              @php $heroType = config('settings.hero_type') ?? 'image'; @endphp
              @if($heroType === 'youtube' && config('settings.hero_youtube_url'))
                <div class="aspect-[4/3] w-full overflow-hidden rounded-3xl border border-neutral-800/50 hover-lift glow">
                  <iframe class="w-full h-full" src="{{ App\Helper::youtubeEmbed(config('settings.hero_youtube_url')) }}" title="Hero video" loading="lazy" allowfullscreen poster="{{ App\Helper::youtubeThumb(config('settings.hero_youtube_url')) }}"></iframe>
                </div>
              @else
                @php $heroSrc = App\Helper::assetUrl(config('settings.hero_image_source'), config('settings.hero_image_url'), config('settings.hero_image_file')); @endphp
                @if($heroSrc)
                  <img src="{{ $heroSrc }}" alt="Hero" class="aspect-[4/3] w-full rounded-3xl border border-neutral-800/50 object-cover hover-lift glow" />
                @else
                  <div class="aspect-[4/3] w-full overflow-hidden rounded-3xl border border-neutral-800/50 animated-gradient hover-lift glow"></div>
                @endif
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="access" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div class="flex items-end justify-between gap-6 mb-10">
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">{{ $t('hp_access_title', 'odeka.access_platform') }}</h2>
      </div>
      <div class="mt-8 grid gap-6 md:grid-cols-3">
        @php
          $access = [
            ['title'=>$t('hp_card_watch_title', 'odeka.card_watch'), 'desc'=>$t('hp_card_watch_desc', 'odeka.card_watch_desc'), 'cta'=>__('odeka.open_platform'), 'href'=>url('channel'), 'icon'=>'play'],
            ['title'=>$t('hp_card_creators_title', 'odeka.card_creators'), 'desc'=>$t('hp_card_creators_desc', 'odeka.card_creators_desc'), 'cta'=>__('odeka.creator_sign_in'), 'href'=>route('login'), 'icon'=>'users'],
            ['title'=>$t('hp_card_advertisers_title', 'odeka.card_advertisers'), 'desc'=>$t('hp_card_advertisers_desc', 'odeka.card_advertisers_desc'), 'cta'=>__('odeka.start_campaign'), 'href'=>url('brief'), 'icon'=>'rocket'],
          ];
        @endphp
        @foreach ($access as $e)
          <div class="group rounded-3xl glass border-neutral-800/50 p-8 hover-lift transition-all hover:border-neutral-700">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold">{{ $e['title'] }}</h3>
              @if($e['icon'] === 'play')
                <svg class="w-6 h-6 text-neutral-600 group-hover:text-neutral-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              @elseif($e['icon'] === 'users')
                <svg class="w-6 h-6 text-neutral-600 group-hover:text-neutral-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              @else
                <svg class="w-6 h-6 text-neutral-600 group-hover:text-neutral-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              @endif
            </div>
            <p class="mt-3 text-sm text-neutral-400 leading-relaxed">{{ $e['desc'] }}</p>
            <div class="mt-6">
              <a href="{{ $e['href'] }}" class="inline-flex text-sm font-medium rounded-full border border-neutral-700 px-5 py-2.5 transition-all hover:border-neutral-500 hover:bg-white/5">
                {{ $e['cta'] }}
              </a>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-2">
      <div class="md:hidden flex items-center justify-center">
        <div class="inline-flex items-center gap-2 p-1 rounded-full border border-neutral-800" id="mobile-tabs">
          <button data-tab="Odeka" class="px-4 py-1.5 text-sm rounded-full transition">Odeka</button>
          <button data-tab="Media" class="px-4 py-1.5 text-sm rounded-full transition">Media</button>
        </div>
      </div>
    </section>

    <div class="tab-pane tab-odeka">
      <section id="advertisers" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid gap-10 lg:grid-cols-12 items-start">
          <div class="lg:col-span-5">
            <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">{{ $t('hp_advertisers_title', 'odeka.advertisers_title') }}</h2>
            <p class="mt-4 text-neutral-300">{{ $t('hp_advertisers_sub', 'odeka.advertisers_sub') }}</p>
            <ul class="mt-6 space-y-3 text-neutral-300">
              <li>• {{ $t('hp_bullet_audience', 'odeka.bullet_audience') }}</li>
              <li>• {{ $t('hp_bullet_story', 'odeka.bullet_story') }}</li>
              <li>• {{ $t('hp_bullet_distribution', 'odeka.bullet_distribution') }}</li>
              <li>• {{ $t('hp_bullet_measurement', 'odeka.bullet_measurement') }}</li>
            </ul>
            <div class="mt-7 flex gap-3">
              <a href="{{ url('brief') }}" class="rounded-full bg-white text-neutral-900 px-5 py-3 text-sm font-medium hover:bg-neutral-200">{{ __('odeka.submit_brief') }}</a>
              <a href="{{ url('media-kit') }}" class="rounded-full border border-neutral-800 px-5 py-3 text-sm hover:border-neutral-700">{{ __('odeka.download_media_kit') }}</a>
            </div>
          </div>
          <div class="lg:col-span-7">
            <div class="grid sm:grid-cols-2 gap-6">
              @php $cards = [
                ['title'=>$t('hp_card_brand_story_title', 'odeka.brand_story'), 'desc'=>$t('hp_card_brand_story_desc', 'odeka.brand_story') ?: 'Short‑form narratives produced by Odeka Studio — from teaser to hero film.'],
                ['title'=>$t('hp_card_creator_partnerships_title', 'odeka.creator_partnerships'), 'desc'=>$t('hp_card_creator_partnerships_desc', 'odeka.creator_partnerships') ?: 'Tap trusted local voices to extend reach and authenticity.'],
                ['title'=>$t('hp_card_event_coverage_title', 'odeka.event_coverage'), 'desc'=>$t('hp_card_event_coverage_desc', 'odeka.event_coverage') ?: 'On‑site capture + same‑day edits for festivals and launches.'],
                ['title'=>$t('hp_card_performance_title', 'odeka.performance_addons'), 'desc'=>$t('hp_card_performance_desc', 'odeka.performance_addons') ?: 'Retargeting, UTM tracking, A/B hooks, caption optimization.'],
              ]; @endphp
              @foreach ($cards as $c)
                <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-5">
                  <div class="text-lg font-medium">{{ $c['title'] }}</div>
                  <p class="mt-2 text-sm text-neutral-300">{{ $c['desc'] }}</p>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </section>

      <section id="earnings" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">{{ __('odeka.sim_title') }}</h2>
          <p class="mt-2 text-neutral-300">{{ __('odeka.sim_sub') }}</p>
        </div>
        <div class="mt-10 grid gap-10 lg:grid-cols-2">
          <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-6">
            <div class="flex items-center justify-between text-sm text-neutral-300">
              <label for="followers" class="font-medium">{{ __('odeka.sim_followers_q') }}</label>
              <span id="followersDisplay" class="tabular-nums text-neutral-400">#300,000</span>
            </div>
            <input id="followers" type="range" min="0" max="1000000" step="1000" value="300000" class="mt-3 w-full accent-white" />
            <div class="mt-8 flex items-center justify-between text-sm text-neutral-300">
              <label for="price" class="font-medium">{{ __('odeka.sim_price_q') }}</label>
              <span id="priceDisplay" class="tabular-nums text-neutral-400">CFA&nbsp;1 500</span>
            </div>
            <input id="price" type="range" min="500" max="20000" step="100" value="1500" class="mt-3 w-full accent-white" />
            <div class="mt-6 text-xs text-neutral-400">{{ __('odeka.sim_note', ['conv'=>5,'fee'=>5]) }}</div>
          </div>
          <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-6 flex flex-col items-center justify-center text-center">
            <div class="text-neutral-300">{{ __('odeka.sim_estimated_subs') }}</div>
            <div id="subsDisplay" class="mt-1 text-2xl font-semibold tabular-nums">15,000</div>
            <div class="mt-6 text-neutral-300">{{ __('odeka.sim_you_could_earn') }}</div>
            <div class="mt-2 text-3xl sm:text-4xl font-semibold tracking-tight tabular-nums" id="netDisplay">CFA&nbsp;21 375 000 <span class="text-neutral-400 text-xl align-middle">{{ __('odeka.per_month') }}</span></div>
            <div class="mt-6 text-xs text-neutral-500 max-w-md">{{ __('odeka.sim_disclaimer', ['conv'=>5,'fee'=>5]) }}</div>
          </div>
        </div>
        <div class="mt-8 text-center text-xs text-neutral-500">{{ __('odeka.need_custom_plan') }} <a href="#contact" class="underline hover:text-white">{{ __('odeka.contact_us') }}</a>.</div>
      </section>

      <section id="campaigns" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex items-end justify-between gap-6">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">{{ __('odeka.campaigns_title') }}</h2>
          @php
            $caseHref = config('settings.case_study_source') === 'url' && config('settings.case_study_url')
              ? config('settings.case_study_url')
              : (config('settings.case_study_file') ? App\Helper::getFile(config('settings.case_study_file')) : url('blog'));
          @endphp
          <a href="{{ $caseHref }}" class="text-sm text-neutral-300 hover:text-white">{{ __('odeka.see_case_study') }}</a>
        </div>
        <div class="mt-10 grid gap-6 lg:grid-cols-12">
          <div class="lg:col-span-7">
            <div class="rounded-3xl border border-neutral-900 p-6 bg-neutral-950">
              <ol class="grid gap-6 lg:grid-cols-2">
                @php $steps=[['name'=>'Brief','detail'=>'Objectives, audience, budget, target markets.'],['name'=>'Story','detail'=>'Creative routes, scripts, casting, visual language.'],['name'=>'Production','detail'=>'Studio or on‑location. Photo + video + design.'],['name'=>'Distribution','detail'=>"O'Channel + creators + paid amplification."],['name'=>'Measurement','detail'=>'Analytics and brand lift study.']]; @endphp
                @foreach ($steps as $i => $s)
                  <li class="rounded-2xl border border-neutral-900/60 p-5 bg-[linear-gradient(180deg,rgba(255,255,255,0.04),transparent)]">
                    <div class="text-sm text-neutral-400">{{ __('odeka.step', ['n'=>$i+1]) }}</div>
                    <div class="mt-1 text-lg font-medium">{{ __([ 'odeka.step_brief','odeka.step_story','odeka.step_production','odeka.step_distribution','odeka.step_measurement'][$i]) }}</div>
                    <p class="mt-2 text-sm text-neutral-300">{{ $s['detail'] }}</p>
                  </li>
                @endforeach
              </ol>
              <div class="mt-6 text-sm text-neutral-400">{{ __('odeka.campaigns_note') }}</div>
            </div>
          </div>
          <div class="lg:col-span-5">
            <div class="rounded-3xl border border-neutral-900 bg-neutral-950 overflow-hidden">
              <div class="aspect-[4/3] bg-[linear-gradient(135deg,rgba(255,255,255,0.08),transparent)]"></div>
              <div class="p-6">
                <div class="text-lg font-medium">{{ __('odeka.case_local_launch') }}</div>
                <p class="mt-2 text-sm text-neutral-300">4‑video story arc, creator collaborations, and paid boosts. Outcome example: +38% visits in 4 weeks, +12% repeat.</p>
            <div class="mt-4"><a href="{{ $caseHref }}" class="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">{{ __('odeka.download_pdf') }}</a></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div class="tab-pane tab-media hidden">
      <section class="border-y border-neutral-900/60">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
          @php $trust=[["k"=>'Shows',"v"=>'6+'],["k"=>'Monthly reach',"v"=>'500K+'],["k"=>'Avg. watch time',"v"=>'3m 12s'],["k"=>'Partners',"v"=>'40+']]; @endphp
          @foreach ($trust as $t)
            <div>
              <div class="text-2xl font-semibold">{{ $t['v'] }}</div>
              <div class="mt-1 text-xs text-neutral-400">{{ $t['k'] }}</div>
            </div>
          @endforeach
        </div>
      </section>

      <section id="channel" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex items-end justify-between gap-6">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">O'Channel — Emissions</h2>
          <a href="{{ url('explore') }}" class="text-sm text-neutral-300 hover:text-white">{{ __('odeka.see_all_shows') }}</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @php
            $cards = [
              ["name"=>"O'Show (flagship)","tag"=>'Interviews • Music • Culture', 'img' => config('settings.img_1') ? asset('img/'.config('settings.img_1')) : null],
              ["name"=>'Street Stories',"tag"=>'People • Places • Food', 'img' => config('settings.img_2') ? asset('img/'.config('settings.img_2')) : null],
              ["name"=>'Creator Spotlight',"tag"=>'Models • Creators • Makers', 'img' => config('settings.img_3') ? asset('img/'.config('settings.img_3')) : null],
              ["name"=>'Business Now',"tag"=>'Entrepreneurs • Playbooks', 'img' => config('settings.img_4') ? asset('img/'.config('settings.img_4')) : null],
            ];
          @endphp
          @foreach ($cards as $c)
            <div class="group relative overflow-hidden rounded-3xl border border-neutral-900 bg-neutral-950">
              @if($c['img'])
                <img src="{{ $c['img'] }}" alt="{{ $c['name'] }}" class="aspect-video w-full object-cover" />
              @else
                <div class="aspect-video w-full bg-[linear-gradient(135deg,rgba(255,255,255,0.08),transparent)]"></div>
              @endif
              <div class="p-5">
                <div class="text-lg font-medium">{{ $c['name'] }}</div>
                <div class="mt-1 text-sm text-neutral-400">{{ $c['tag'] }}</div>
                <div class="mt-4"><a href="{{ url('explore') }}" class="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">{{ __('odeka.watch_episodes') }}</a></div>
              </div>
            </div>
          @endforeach
        </div>
      </section>

      <section id="oshow" class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10">
          <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-neutral-800 to-transparent"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
          <div class="grid gap-10 lg:grid-cols-12 items-center">
            <div class="lg:col-span-6 order-2 lg:order-1">
              <h3 class="text-3xl sm:text-4xl font-semibold tracking-tight">{{ __('odeka.oshow_star') }}</h3>
              <p class="mt-4 text-neutral-300">{{ __('odeka.oshow_desc') }}</p>
              <ul class="mt-6 space-y-3 text-neutral-300">
                <li>• {{ __('odeka.oshow_sponsorship') }}</li>
                <li>• {{ __('odeka.oshow_deliverables') }}</li>
                <li>• {{ __('odeka.oshow_options') }}</li>
              </ul>
              <div class="mt-7 flex gap-3">
                <a href="{{ url('sponsor/oshow') }}" class="rounded-full bg-white text-neutral-900 px-5 py-3 text-sm font-medium hover:bg-neutral-200">{{ __('odeka.get_sponsor_kit') }}</a>
                <a href="{{ url('channel/o-show/latest') }}" class="rounded-full border border-neutral-800 px-5 py-3 text-sm hover:border-neutral-700">{{ __('odeka.watch_latest_episode') }}</a>
              </div>
            </div>
            <div class="lg:col-span-6 order-1 lg:order-2">
              @php
                $oshowType = config('settings.oshow_media_type') ?? 'image';
                $oshowYt = config('settings.oshow_youtube_url');
                $oshowImg = App\Helper::assetUrl(config('settings.oshow_image_source'), config('settings.oshow_image_url'), config('settings.oshow_image_file'));
              @endphp
              @if($oshowType === 'youtube' && $oshowYt)
                <div class="aspect-[16/10] w-full overflow-hidden rounded-3xl border border-neutral-900 relative">
                  <img src="{{ App\Helper::youtubeThumb($oshowYt, 'sddefault') }}" alt="O'Show" class="w-full h-full object-cover" />
                  <iframe class="absolute inset-0 w-full h-full" src="{{ App\Helper::youtubeEmbed($oshowYt) }}" title="O'Show" loading="lazy" allowfullscreen></iframe>
                </div>
              @elseif($oshowImg)
                <img src="{{ $oshowImg }}" alt="O'Show" class="aspect-[16/10] w-full rounded-3xl border border-neutral-900 object-cover" />
              @else
                <div class="aspect-[16/10] w-full overflow-hidden rounded-3xl border border-neutral-900 bg-[radial-gradient(closest-side,rgba(255,255,255,0.10),transparent_70%)]"></div>
              @endif
            </div>
          </div>
        </div>
      </section>
    </div>

    <footer id="contact" class="border-t border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid gap-10 md:grid-cols-4">
          <div>
            <div class="flex items-center gap-3">
              @php $logo = config('settings.logo') ? asset('img/'.config('settings.logo')) : null; @endphp
              @if($logo)
                <img src="{{ $logo }}" alt="Odeka logo" class="h-7 w-auto" />
              @else
                <div class="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0"></div>
              @endif
              <span class="font-semibold tracking-tight">{{ __('odeka.brand') }}</span>
            </div>
            <p class="mt-4 text-sm text-neutral-400 max-w-xs">{{ __('odeka.hero_headline') }}</p>
          </div>
          @php $links=[
            __('odeka.company')=>[
              __('odeka.about')=>'/p/about',
              __('odeka.contact')=>'/contact'
            ],
            __('odeka.products')=>[
              'Odeka'=>'/',
              "O'Channel"=>'/channel',
              "O'Show"=>'/channel/o-show/latest',
              'Studio'=>'/studio'
            ],
            __('odeka.advertisers')=>[
              __('odeka.media_kit')=>'/media-kit',
              __('odeka.pricing')=>'/pricing',
              __('odeka.case_studies')=>'/case-study'
            ],
            __('odeka.legal')=>[
              __('odeka.privacy')=>'/p/privacy',
              __('odeka.tos')=>'/p/terms-of-service',
              __('odeka.cookies')=>'/p/cookies',
              __('odeka.how_it_works')=>'/p/how-it-works'
            ]
          ]; @endphp
          @foreach ($links as $k=>$arr)
            <div>
              <div class="text-sm font-medium text-neutral-300">{{ $k }}</div>
              <ul class="mt-3 space-y-2 text-sm text-neutral-400">
                @foreach ($arr as $label=>$href)
                  <li><a href="{{ url($href) }}" class="hover:text-white">{{ $label }}</a></li>
                @endforeach
              </ul>
            </div>
          @endforeach
        </div>
        <div class="mt-8 grid gap-4 sm:flex sm:items-center sm:justify-between">
          <div class="flex flex-wrap items-center gap-4">
            <label for="odeka-lang" class="text-xs text-neutral-400">{{ __('odeka.language') }}</label>
            <select id="odeka-lang" class="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700">
              <option value="en">English</option>
              <option value="fr">Français</option>
            </select>
            <label for="odeka-currency" class="ml-2 text-xs text-neutral-400">{{ __('odeka.currency') }}</label>
            <form id="currency-form" method="POST" action="{{ route('currency.switch') }}">
              @csrf
              <select id="odeka-currency" name="currency" class="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700">
                <option value="XOF">CFA (XOF)</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
              </select>
            </form>
          </div>
          <div class="text-xs text-neutral-500">{{ __('odeka.made_with_care') }}</div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-500">
          <div>© {{ date('Y') }} Odeka Media. All rights reserved.</div>
          <div></div>
        </div>
      </div>
    </footer>

    <script>
      const state = {
        tab: document.body.dataset.tab || 'Odeka',
        locale: document.body.dataset.locale || 'en',
        currency: document.body.dataset.currency || 'XOF',
        conversion: 0.05,
        platformFee: 0.05,
      };
      const fmt = (n) => {
        try { return new Intl.NumberFormat(state.locale, { style: 'currency', currency: state.currency, maximumFractionDigits: 2 }).format(n); }
        catch { return state.currency + ' ' + Number(n).toFixed(2); }
      };
      const byId = (id) => document.getElementById(id);
      function applyTabUI() {
        const applyActive = (btn) => {
          const active = btn.dataset.tab === state.tab;
          btn.classList.toggle('bg-white', active);
          btn.classList.toggle('text-neutral-900', active);
          btn.classList.toggle('text-neutral-300', !active);
        };
        document.querySelectorAll('#header-tabs [data-tab], #mobile-tabs [data-tab]').forEach((btn) => applyActive(btn));
        document.querySelector('.tab-odeka').classList.toggle('hidden', state.tab !== 'Odeka');
        document.querySelector('.tab-media').classList.toggle('hidden', state.tab !== 'Media');
      }
      document.querySelectorAll('#header-tabs [data-tab], #mobile-tabs [data-tab]').forEach((btn) => {
        btn.addEventListener('click', () => { state.tab = btn.dataset.tab; applyTabUI(); });
      });
      const followersEl = byId('followers');
      const priceEl = byId('price');
      const followersDisplay = byId('followersDisplay');
      const priceDisplay = byId('priceDisplay');
      const subsDisplay = byId('subsDisplay');
      const netDisplay = byId('netDisplay');
      function recompute() {
        const followers = Number(followersEl.value || 0);
        const price = Number(priceEl.value || 0);
        const subs = Math.round(followers * state.conversion);
        const gross = subs * price;
        const net = gross * (1 - state.platformFee);
        followersDisplay.textContent = '#' + followers.toLocaleString();
        priceDisplay.textContent = fmt(price);
        subsDisplay.textContent = subs.toLocaleString();
        netDisplay.innerHTML = `${fmt(net)} <span class="text-neutral-400 text-xl align-middle">per month</span>`;
      }
      followersEl.addEventListener('input', recompute);
      priceEl.addEventListener('input', recompute);
      // Simulator defaults from admin settings
      @php
        $simRanges = config('settings.sim_price_ranges_json');
        if (is_string($simRanges)) {
          $simRanges = json_decode($simRanges, true) ?: [];
        }
        if (!is_array($simRanges)) {
          $simRanges = [];
        }
      @endphp
      
      window.SIM = {
        conversion: {{ (float) (config('settings.sim_default_conversion') ?? 0.05) }},
        fee: {{ (float) (config('settings.sim_platform_fee') ?? 0.05) }},
        ranges: {!! json_encode($simRanges) !!}
      };
      
      // Apply admin defaults
      if (SIM.conversion > 0) state.conversion = SIM.conversion;
      if (SIM.fee > 0) state.platformFee = SIM.fee;
      
      // Apply currency-specific price ranges
      function applyPriceRanges() {
        if (SIM.ranges && SIM.ranges[state.currency]) {
          const r = SIM.ranges[state.currency];
          if (r.min != null) {
            priceEl.min = r.min;
            priceEl.value = Math.max(priceEl.value, r.min);
          }
          if (r.max != null) {
            priceEl.max = r.max;
            priceEl.value = Math.min(priceEl.value, r.max);
          }
          if (r.step != null) {
            priceEl.step = r.step;
          }
        }
      }
      
      const langSel = document.getElementById('odeka-lang');
      const curSel = document.getElementById('odeka-currency');
      if (langSel) langSel.value = state.locale;
      if (curSel) curSel.value = state.currency;
      
      langSel.addEventListener('change', () => {
        const code = langSel.value || 'en';
        window.location.href = `${'{{ url('change/lang') }}'}/${code}`;
      });
      
      // Handle currency change - update state and apply ranges before submitting
      curSel.addEventListener('change', () => {
        state.currency = curSel.value;
        applyPriceRanges();
        recompute();
        // Submit form to persist currency change
        document.getElementById('currency-form').submit();
      });
      
      applyPriceRanges();
      applyTabUI();
      recompute();
    </script>
  </body>
 </html>


