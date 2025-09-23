<?php
// Odeka — Login Page (PHP rewrite of the React version)
// Uses Tailwind CDN and vanilla JS. Works standalone or inside Laravel.
// If Laravel helpers exist, they are used; otherwise we fall back to env/defaults.

$hasRoute = function_exists('route');
$homeUrl  = $hasRoute ? route('home') : ($_ENV['HOME_URL'] ?? '/');
$loginUrl = $hasRoute ? route('login') : ($_ENV['LOGIN_URL'] ?? '/login');
$regUrl   = ($hasRoute && function_exists('Route') && \Route::has('register')) ? route('register') : ($_ENV['REGISTER_URL'] ?? '/register');
$forgotUrl= ($hasRoute && function_exists('Route') && \Route::has('password.request')) ? route('password.request') : ($_ENV['FORGOT_URL'] ?? '/password/forgot');

$localeAction   = ($hasRoute && function_exists('Route') && \Route::has('locale.switch')) ? route('locale.switch') : ($_ENV['LOCALE_ACTION'] ?? '/locale');
$currencyAction = ($hasRoute && function_exists('Route') && \Route::has('currency.switch')) ? route('currency.switch') : ($_ENV['CURRENCY_ACTION'] ?? '/currency');

$csrf = function_exists('csrf_token') ? csrf_token() : '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login — Odeka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.tabular-nums{font-variant-numeric:tabular-nums}</style>
  </head>
  <body class="min-h-screen bg-neutral-950 text-neutral-100 selection:bg-neutral-800 selection:text-white">
    <!-- Header -->
    <header class="sticky top-0 z-40 backdrop-blur border-b border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <a href="<?= htmlspecialchars($homeUrl) ?>" class="flex items-center gap-3">
            <span class="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0 shadow-[0_0_30px_-10px_rgba(255,255,255,0.7)]"></span>
            <span class="font-semibold tracking-tight">Odeka</span>
          </a>
          <nav class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($homeUrl) ?>" class="hidden sm:inline-flex rounded-full border border-neutral-800 px-4 py-2 text-sm hover:border-neutral-700">Back to home</a>
            <a href="<?= htmlspecialchars($regUrl) ?>" class="inline-flex rounded-full bg-white text-neutral-900 px-4 py-2 text-sm font-medium hover:bg-neutral-200">Get started</a>
          </nav>
        </div>
      </div>
    </header>

    <!-- Background + Login Card -->
    <section class="relative overflow-hidden">
      <div class="relative">
        <div class="pointer-events-none absolute inset-0 -z-10">
          <div class="absolute left-1/2 top-[-10%] h-[700px] w-[900px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(255,255,255,0.12),transparent_70%)] blur-2xl"></div>
          <div class="absolute right-[-10%] bottom-[-20%] h-[500px] w-[500px] rounded-full bg-[conic-gradient(from_180deg_at_50%_50%,rgba(255,255,255,0.08),transparent)] blur-2xl"></div>
        </div>

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
          <section class="mx-auto w-full max-w-md rounded-3xl border border-neutral-900 bg-neutral-950/60 backdrop-blur p-6 sm:p-8 shadow-[0_10px_40px_-20px_rgba(0,0,0,0.6)]">
            <h1 class="text-center text-2xl sm:text-3xl font-semibold tracking-tight">Welcome back</h1>
            <p class="mt-1 text-center text-sm text-neutral-400">Happy to see you again.</p>

            <form class="mt-6 space-y-4" method="post" action="<?= htmlspecialchars($loginUrl) ?>" novalidate>
              <?php if($csrf): ?><input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>" /><?php endif; ?>

              <div>
                <label for="username" class="block text-sm text-neutral-300">Username or Email</label>
                <input id="username" name="username" type="text" autocomplete="username"
                       class="mt-1 w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 text-sm outline-none focus:border-neutral-600" required />
              </div>

              <div>
                <label for="password" class="block text-sm text-neutral-300">Password</label>
                <div class="mt-1 relative">
                  <input id="password" name="password" type="password" autocomplete="current-password"
                         class="w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 pr-14 text-sm outline-none focus:border-neutral-600" required />
                  <button type="button" id="togglePass" aria-label="Show password"
                          class="absolute inset-y-0 right-2 my-auto rounded-lg border border-neutral-800 px-2 text-xs text-neutral-300 hover:border-neutral-700">Show</button>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-neutral-300">
                  <input type="checkbox" name="remember" class="h-4 w-4 rounded border-neutral-700 bg-neutral-950" />
                  Remember me
                </label>
                <a class="text-sm text-neutral-300 hover:text-white" href="<?= htmlspecialchars($forgotUrl) ?>">Forgot password?</a>
              </div>

              <button type="submit" class="w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">Login</button>

              <p class="text-center text-sm text-neutral-400">Don't have an account? <a class="underline hover:text-white" href="<?= htmlspecialchars($regUrl) ?>">Sign up</a></p>
            </form>
          </section>
        </main>
      </div>
    </section>

    <!-- Footer with language & currency controls -->
    <footer class="border-t border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <span class="h-6 w-6 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0"></span>
            <span class="text-sm text-neutral-400">© <?= date('Y') ?> Odeka. All rights reserved.</span>
          </div>
          <div class="flex items-center gap-6">
            <form method="post" action="<?= htmlspecialchars($localeAction) ?>" class="flex items-center gap-2">
              <?php if($csrf): ?><input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>" /><?php endif; ?>
              <label for="locale" class="text-xs text-neutral-400">Language</label>
              <select id="locale" name="locale" class="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700">
                <option value="en-US">English</option>
                <option value="fr-FR">Français</option>
              </select>
            </form>
            <form method="post" action="<?= htmlspecialchars($currencyAction) ?>" class="flex items-center gap-2">
              <?php if($csrf): ?><input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>" /><?php endif; ?>
              <label for="currency" class="text-xs text-neutral-400">Currency</label>
              <select id="currency" name="currency" class="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700">
                <option value="XOF">CFA (XOF)</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
              </select>
            </form>
          </div>
        </div>
      </div>
    </footer>

    <script>
      // Show/Hide password toggle
      const toggle = document.getElementById('togglePass');
      const input  = document.getElementById('password');
      if (toggle && input) {
        toggle.addEventListener('click', () => {
          const show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          toggle.textContent = show ? 'Hide' : 'Show';
          toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
      }
    </script>
  </body>
</html>
