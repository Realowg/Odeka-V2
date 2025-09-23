<?php
// Odeka Media — Homepage (PHP version)
// Tech: PHP + Tailwind (CDN) + vanilla JS for interactivity
// Mirrors the React canvas version: two tabs (Odeka/Media), Access hub, Earnings Simulator,
// Trust metrics, Channel, O'Show spotlight, Footer with language & currency controls.
//
// Server defaults (can be overridden via query params if desired)
$tab      = isset($_GET['tab']) && in_array($_GET['tab'], ['Odeka','Media']) ? $_GET['tab'] : 'Odeka';
$locale   = isset($_GET['locale']) ? $_GET['locale'] : 'en-US';
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'XOF';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Odeka Media</title>
    <!-- Tailwind CDN (for demo). In production, compile your CSS. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* helpers */
      .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
  </head>
  <body class="min-h-screen bg-neutral-950 text-neutral-100 selection:bg-neutral-800 selection:text-white" data-tab="<?= htmlspecialchars($tab) ?>" data-locale="<?= htmlspecialchars($locale) ?>" data-currency="<?= htmlspecialchars($currency) ?>">
    <!-- Header -->
    <div class="sticky top-0 z-40 backdrop-blur border-b border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <!-- Brand -->
          <a href="#top" class="flex items-center gap-3">
            <div class="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0 shadow-[0_0_30px_-10px_rgba(255,255,255,0.7)]"></div>
            <span class="font-semibold tracking-tight">Odeka Media</span>
          </a>

          <!-- Tabs -->
          <div class="hidden md:flex items-center gap-2 p-1 rounded-full border border-neutral-800" id="header-tabs">
            <button data-tab="Odeka" class="px-4 py-1.5 text-sm rounded-full transition">Odeka</button>
            <button data-tab="Media" class="px-4 py-1.5 text-sm rounded-full transition">Media</button>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3">
            <a href="#signin" class="hidden sm:inline-flex rounded-full border border-neutral-800 px-4 py-2 text-sm hover:border-neutral-700">Sign in</a>
            <a href="#open-app" class="inline-flex rounded-full bg-white text-neutral-900 px-4 py-2 text-sm font-medium hover:bg-neutral-200">Open the app</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Hero -->
    <section class="relative overflow-hidden">
      <div class="relative">
        <div class="pointer-events-none absolute inset-0 -z-10">
          <div class="absolute left-1/2 top-[-10%] h-[700px] w-[900px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(255,255,255,0.12),transparent_70%)] blur-2xl"></div>
          <div class="absolute right-[-10%] bottom-[-20%] h-[500px] w-[500px] rounded-full bg-[conic-gradient(from_180deg_at_50%_50%,rgba(255,255,255,0.08),transparent)] blur-2xl"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
          <div class="grid items-center gap-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
              <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight">We create, distribute & monetize content for brands and creators.</h1>
              <p class="mt-5 max-w-2xl text-neutral-300 leading-relaxed">Odeka Media is a content studio and platform. We craft story‑first campaigns, produce original shows, and turn attention into revenue.</p>
              <div class="mt-8 flex flex-wrap gap-3">
                <a href="#watch" class="inline-flex items-center rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">Watch on O'Channel</a>
                <a href="#signin" class="inline-flex items-center rounded-full border border-neutral-800 px-6 py-3 text-sm hover:border-neutral-700">Creator sign in</a>
                <a href="#brief" class="inline-flex items-center rounded-full border border-neutral-800 px-6 py-3 text-sm hover:border-neutral-700">Start a campaign</a>
              </div>
              <p class="mt-6 text-xs text-neutral-400">Trusted by advertisers, creators, and partners.</p>
            </div>
            <div class="lg:col-span-5">
              <div class="aspect-[4/3] w-full overflow-hidden rounded-3xl border border-neutral-800 bg-gradient-to-br from-neutral-900 to-neutral-800 p-2">
                <div class="h-full w-full rounded-2xl bg-neutral-950 relative">
                  <div class="absolute inset-0 grid place-items-center">
                    <div class="text-center">
                      <div class="mx-auto mb-4 h-12 w-12 rounded-xl bg-neutral-800"></div>
                      <p class="text-sm text-neutral-400">Promo reel placeholder</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Access Hub -->
    <section id="access" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
      <div class="flex items-end justify-between gap-6">
        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight">Access the platform</h2>
      </div>
      <div class="mt-6 grid gap-6 md:grid-cols-3">
        <?php
          $access = [
            ['title'=>"Watch O'Channel", 'desc'=>"Open the platform to watch episodes and shorts.", 'cta'=>"Open platform", 'href'=>'#open-app'],
            ['title'=>"Creators", 'desc'=>"Sign in to manage episodes, assets, and analytics.", 'cta'=>"Creator sign in", 'href'=>'#signin'],
            ['title'=>"Advertisers", 'desc'=>"Start a story‑driven campaign with measurable outcomes.", 'cta'=>"Start a campaign", 'href'=>'#brief'],
          ];
          foreach ($access as $e): ?>
          <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-6">
            <div class="text-lg font-medium"><?= htmlspecialchars($e['title']) ?></div>
            <p class="mt-2 text-sm text-neutral-300"><?= htmlspecialchars($e['desc']) ?></p>
            <div class="mt-4">
              <a href="<?= htmlspecialchars($e['href']) ?>" class="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700"><?= htmlspecialchars($e['cta']) ?></a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Mobile Tab Switcher -->
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-2">
      <div class="md:hidden flex items-center justify-center">
        <div class="inline-flex items-center gap-2 p-1 rounded-full border border-neutral-800" id="mobile-tabs">
          <button data-tab="Odeka" class="px-4 py-1.5 text-sm rounded-full transition">Odeka</button>
          <button data-tab="Media" class="px-4 py-1.5 text-sm rounded-full transition">Media</button>
        </div>
      </div>
    </section>

    <!-- Tabs Content -->
    <div class="tab-pane tab-odeka">
      <!-- Advertisers -->
      <section id="advertisers" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid gap-10 lg:grid-cols-12 items-start">
          <div class="lg:col-span-5">
            <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Odeka for Advertisers</h2>
            <p class="mt-4 text-neutral-300">Story‑driven formats + measurable outcomes. Activate your brand across our shows and creator network, from quick‑turn promos to multi‑episode sponsorships.</p>
            <ul class="mt-6 space-y-3 text-neutral-300">
              <li>• Audience targeting: geo, interests, language (FR/EN/Eʋe)</li>
              <li>• Storytelling: native segments, integrations, product placement</li>
              <li>• Distribution: O'Channel, partners, paid boosts, owned placements</li>
              <li>• Measurement: view‑through, brand lift, conversions</li>
            </ul>
            <div class="mt-7 flex gap-3">
              <a href="#brief" class="rounded-full bg-white text-neutral-900 px-5 py-3 text-sm font-medium hover:bg-neutral-200">Submit a brief</a>
              <a href="#media-kit" class="rounded-full border border-neutral-800 px-5 py-3 text-sm hover:border-neutral-700">Download media kit</a>
            </div>
          </div>
          <div class="lg:col-span-7">
            <div class="grid sm:grid-cols-2 gap-6">
              <?php $cards = [
                ['title'=>'Brand storytelling', 'desc'=>'Short‑form narratives produced by Odeka Studio — from teaser to hero film.'],
                ['title'=>'Creator partnerships', 'desc'=>'Tap trusted local voices to extend reach and authenticity.'],
                ['title'=>'Event coverage', 'desc'=>'On‑site capture + same‑day edits for festivals and launches.'],
                ['title'=>'Performance add‑ons', 'desc'=>'Retargeting, UTM tracking, A/B hooks, caption optimization.'],
              ];
              foreach ($cards as $c): ?>
                <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-5">
                  <div class="text-lg font-medium"><?= htmlspecialchars($c['title']) ?></div>
                  <p class="mt-2 text-sm text-neutral-300"><?= htmlspecialchars($c['desc']) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>

      <!-- Earnings Simulator -->
      <section id="earnings" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Creators Earnings Simulator</h2>
          <p class="mt-2 text-neutral-300">Estimate monthly revenue based on your audience and subscription price.</p>
        </div>

        <div class="mt-10 grid gap-10 lg:grid-cols-2">
          <!-- Controls -->
          <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-6">
            <div class="flex items-center justify-between text-sm text-neutral-300">
              <label for="followers" class="font-medium">Number of followers?</label>
              <span id="followersDisplay" class="tabular-nums text-neutral-400">#300,000</span>
            </div>
            <input id="followers" type="range" min="0" max="1000000" step="1000" value="300000" class="mt-3 w-full accent-white" />

            <div class="mt-8 flex items-center justify-between text-sm text-neutral-3 00">
              <label for="price" class="font-medium">Monthly subscription price?</label>
              <span id="priceDisplay" class="tabular-nums text-neutral-400">CFA 1 500</span>
            </div>
            <input id="price" type="range" min="500" max="20000" step="100" value="1500" class="mt-3 w-full accent-white" />

            <div class="mt-6 text-xs text-neutral-400">
              Conversion assumed: <span id="convPct">5</span>% of followers subscribe. Platform fee: <span id="feePct">5</span>% deducted. Payment processor fees not included.
            </div>
          </div>

          <!-- Result -->
          <div class="rounded-3xl border border-neutral-900 bg-neutral-950 p-6 flex flex-col items-center justify-center text-center">
            <div class="text-neutral-300">Estimated subscribers</div>
            <div id="subsDisplay" class="mt-1 text-2xl font-semibold tabular-nums">15,000</div>

            <div class="mt-6 text-neutral-300">You could earn an estimated</div>
            <div class="mt-2 text-3xl sm:text-4xl font-semibold tracking-tight tabular-nums" id="netDisplay">CFA 21 375 000 <span class="text-neutral-400 text-xl align-middle">per month</span></div>

            <div class="mt-6 text-xs text-neutral-500 max-w-md">
              * Estimate only. Based on 5% of followers who subscribe. Does not include payment processor fees. Net amount reflects a 5% platform fee.
            </div>
          </div>
        </div>

        <div class="mt-8 text-center text-xs text-neutral-500">
          Want a custom plan for higher volumes or bundles? <a href="#contact" class="underline hover:text-white">Contact us</a>.
        </div>
      </section>

      <!-- Campaigns -->
      <section id="campaigns" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex items-end justify-between gap-6">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Marketing campaigns with brand storytelling</h2>
          <a href="#case-study" class="text-sm text-neutral-300 hover:text-white">See case study →</a>
        </div>
        <div class="mt-10 grid gap-6 lg:grid-cols-12">
          <div class="lg:col-span-7">
            <div class="rounded-3xl border border-neutral-900 p-6 bg-neutral-950">
              <ol class="grid gap-6 lg:grid-cols-2">
                <?php $steps=[
                  ['name'=>'Brief','detail'=>'Objectives, audience, budget, target markets.'],
                  ['name'=>'Story','detail'=>'Creative routes, scripts, casting, visual language.'],
                  ['name'=>'Production','detail'=>'Studio or on‑location. Photo + video + design.'],
                  ['name'=>'Distribution','detail'=>"O'Channel + creators + paid amplification."],
                  ['name'=>'Measurement','detail'=>'Analytics and brand lift study.'],
                ]; foreach ($steps as $i=>$s): ?>
                  <li class="rounded-2xl border border-neutral-900/60 p-5 bg-[linear-gradient(180deg,rgba(255,255,255,0.04),transparent)]">
                    <div class="text-sm text-neutral-400">Step <?= $i+1 ?></div>
                    <div class="mt-1 text-lg font-medium"><?= htmlspecialchars($s['name']) ?></div>
                    <p class="mt-2 text-sm text-neutral-300"><?= htmlspecialchars($s['detail']) ?></p>
                  </li>
                <?php endforeach; ?>
              </ol>
              <div class="mt-6 text-sm text-neutral-400">We deliver media that feels native to the culture and the platform.</div>
            </div>
          </div>
          <div class="lg:col-span-5">
            <div class="rounded-3xl border border-neutral-900 bg-neutral-950 overflow-hidden">
              <div class="aspect-[4/3] bg-[linear-gradient(135deg,rgba(255,255,255,0.08),transparent)]"></div>
              <div class="p-6">
                <div class="text-lg font-medium">Case study — Local Launch</div>
                <p class="mt-2 text-sm text-neutral-300">4‑video story arc, creator collaborations, and paid boosts. Outcome example: +38% visits in 4 weeks, +12% repeat.</p>
                <div class="mt-4"><a href="#download-pdf" class="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">Download PDF</a></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="overflow-hidden rounded-3xl border border-neutral-900 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),transparent)]">
          <div class="p-8 sm:p-12 lg:p-16 text-center">
            <h3 class="text-2xl sm:text-3xl font-semibold tracking-tight">Ready to launch your next campaign?</h3>
            <p class="mt-3 text-neutral-300">Send your brief — we’ll reply quickly with a proposal and timeline.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
              <a href="#brief" class="rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">Submit a brief</a>
              <a href="#call" class="rounded-full border border-neutral-800 px-6 py-3 text-sm hover:border-neutral-700">Book a call</a>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div class="tab-pane tab-media hidden">
      <!-- Trust metrics -->
      <section class="border-y border-neutral-900/60">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
          <?php $trust=[['k'=>'Shows','v'=>'6+'],['k'=>'Monthly reach','v'=>'500K+'],['k'=>'Avg. watch time','v'=>'3m 12s'],['k'=>'Partners','v'=>'40+']]; foreach($trust as $t): ?>
            <div>
              <div class="text-2xl font-semibold"><?= $t['v'] ?></div>
              <div class="mt-1 text-xs text-neutral-400"><?= $t['k'] ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Channel -->
      <section id="channel" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex items-end justify-between gap-6">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">O'Channel — Emissions</h2>
          <a href="#channel-all" class="text-sm text-neutral-300 hover:text-white">See all shows →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <?php $shows=[
            ["name"=>"O'Show (flagship)","tag"=>"Interviews • Music • Culture"],
            ["name"=>"Street Stories","tag"=>"People • Places • Food"],
            ["name"=>"Creator Spotlight","tag"=>"Models • Creators • Makers"],
            ["name"=>"Business Now","tag"=>"Entrepreneurs • Playbooks"],
          ]; foreach($shows as $s): ?>
            <div class="group relative overflow-hidden rounded-3xl border border-neutral-900 bg-neutral-950">
              <div class="aspect-video w-full bg-[linear-gradient(135deg,rgba(255,255,255,0.08),transparent)]"></div>
              <div class="p-5">
                <div class="text-lg font-medium"><?= htmlspecialchars($s['name']) ?></div>
                <div class="mt-1 text-sm text-neutral-400"><?= htmlspecialchars($s['tag']) ?></div>
                <div class="mt-4"><a href="#" class="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">Watch episodes</a></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- O'Show -->
      <section id="oshow" class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10">
          <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-neutral-800 to-transparent"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
          <div class="grid gap-10 lg:grid-cols-12 items-center">
            <div class="lg:col-span-6 order-2 lg:order-1">
              <h3 class="text-3xl sm:text-4xl font-semibold tracking-tight">O'Show — Star Emission</h3>
              <p class="mt-4 text-neutral-300">The centerpiece of our lineup. Long‑form interviews with artists and creators shaping culture. Available with sponsor integrations, live audience tapings, and short‑form cut‑downs.</p>
              <ul class="mt-6 space-y-3 text-neutral-300">
                <li>• Sponsorship tiers: opening tag, mid‑roll segment, end‑card</li>
                <li>• Deliverables: full episode + shorts + stills + captions</li>
                <li>• Options: live studio audience, giveaway, meet‑and‑greet</li>
              </ul>
              <div class="mt-7 flex gap-3">
                <a href="#sponsor-oshow" class="rounded-full bg-white text-neutral-900 px-5 py-3 text-sm font-medium hover:bg-neutral-200">Get sponsorship kit</a>
                <a href="#watch-oshow" class="rounded-full border border-neutral-800 px-5 py-3 text-sm hover:border-neutral-700">Watch latest episode</a>
              </div>
            </div>
            <div class="lg:col-span-6 order-1 lg:order-2">
              <div class="aspect-[16/10] w-full overflow-hidden rounded-3xl border border-neutral-900 bg-[radial-gradient(closest-side,rgba(255,255,255,0.10),transparent_70%)]"></div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Footer -->
    <footer id="contact" class="border-t border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid gap-10 md:grid-cols-4">
          <div>
            <div class="flex items-center gap-3">
              <div class="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0"></div>
              <span class="font-semibold tracking-tight">Odeka Media</span>
            </div>
            <p class="mt-4 text-sm text-neutral-400 max-w-xs">We create, distribute, and monetize content that moves culture.</p>
          </div>
          <?php $links=[
            'Company'=>['About','Careers','Contact'],
            'Products'=>['Odeka',"O'Channel","O'Show",'Studio'],
            'Advertisers'=>['Media kit','Pricing','Case studies'],
            'Legal'=>['Privacy','Terms'],
          ]; foreach($links as $k=>$arr): ?>
            <div>
              <div class="text-sm font-medium text-neutral-300"><?= htmlspecialchars($k) ?></div>
              <ul class="mt-3 space-y-2 text-sm text-neutral-400">
                <?php foreach($arr as $v): ?>
                  <li><a href="#" class="hover:text-white"><?= htmlspecialchars($v) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="mt-8 grid gap-4 sm:flex sm:items-center sm:justify-between">
          <div class="flex flex-wrap items-center gap-4">
            <label for="odeka-lang" class="text-xs text-neutral-400">Language</label>
            <select id="odeka-lang" class="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700">
              <option value="en-US">English</option>
              <option value="fr-FR">Français</option>
            </select>

            <label for="odeka-currency" class="ml-2 text-xs text-neutral-400">Currency</label>
            <select id="odeka-currency" class="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700">
              <option value="XOF">CFA (XOF)</option>
              <option value="USD">USD</option>
              <option value="EUR">EUR</option>
            </select>
          </div>

          <div class="text-xs text-neutral-500">Made with care.</div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-500">
          <div>© <?php echo date('Y'); ?> Odeka Media. All rights reserved.</div>
          <div></div>
        </div>
      </div>
    </footer>

    <script>
      // ------- State
      const state = {
        tab: document.body.dataset.tab || 'Odeka',
        locale: document.body.dataset.locale || 'en-US',
        currency: document.body.dataset.currency || 'XOF',
        conversion: 0.05,
        platformFee: 0.05,
      };

      // ------- Utils
      const fmt = (n) => {
        try { return new Intl.NumberFormat(state.locale, { style: 'currency', currency: state.currency, maximumFractionDigits: 2 }).format(n); }
        catch { return state.currency + ' ' + Number(n).toFixed(2); }
      };

      const byId = (id) => document.getElementById(id);

      // ------- Tab UI
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

      // ------- Earnings simulator logic
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

      // ------- Language/Currency selectors
      const langSel = document.getElementById('odeka-lang');
      const curSel = document.getElementById('odeka-currency');
      if (langSel) langSel.value = state.locale;
      if (curSel) curSel.value = state.currency;

      langSel.addEventListener('change', () => { state.locale = langSel.value; recompute(); });
      curSel.addEventListener('change', () => { state.currency = curSel.value; recompute(); });

      // ------- Init
      applyTabUI();
      recompute();
    </script>

<?php
// ----------------- SIMPLE CLI TESTS (run: `php thisfile.php`)
if (php_sapi_name() === 'cli') {
  function simulate($followers, $price, $conversion = 0.05, $fee = 0.05) {
    $subs = (int) round($followers * $conversion);
    $gross = $subs * $price;
    $net = $gross * (1 - $fee);
    return ['subs'=>$subs, 'gross'=>$gross, 'net'=>$net];
  }
  // Basic checks
  assert(simulate(1000, 1000)['subs'] === 50);
  assert(abs(simulate(300000, 1500)['net'] - (round(300000*0.05)*1500*0.95)) < 0.0001);
  fwrite(STDERR, "\n[Tests] Simulator math checks passed.\n");
}
?>
  </body>
</html>
