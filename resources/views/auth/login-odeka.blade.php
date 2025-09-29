<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('auth.login') }} — {{ config('settings.title') }}</title>
    <script>window.tailwind={config:{corePlugins:{preflight:false}}};</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.tabular-nums{font-variant-numeric:tabular-nums}</style>
  </head>
  <body class="min-h-screen bg-neutral-950 text-neutral-100 selection:bg-neutral-800 selection:text-white">
    <header class="sticky top-0 z-40 backdrop-blur border-b border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/'.($settings->logo ?? '')) }}" alt="Logo" class="h-7 w-auto" />
            <span class="font-semibold tracking-tight">{{ config('settings.title') }}</span>
          </a>
          <nav class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="hidden sm:inline-flex rounded-full border border-neutral-800 px-4 py-2 text-sm hover:border-neutral-700">{{ __('general.home') }}</a>
            @if ($settings->registration_active == '1')
              <a href="{{ route('register') }}" class="inline-flex rounded-full bg-white text-neutral-900 px-4 py-2 text-sm font-medium hover:bg-neutral-200">{{ __('general.getting_started') }}</a>
            @endif
          </nav>
        </div>
      </div>
    </header>

    <main class="relative overflow-hidden">
      <div class="relative">
        <div class="pointer-events-none absolute inset-0 -z-10">
          <div class="absolute left-1/2 top-[-10%] h-[700px] w-[900px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(255,255,255,0.12),transparent_70%)] blur-2xl"></div>
          <div class="absolute right-[-10%] bottom-[-20%] h-[500px] w-[500px] rounded-full bg-[conic-gradient(from_180deg_at_50%_50%,rgba(255,255,255,0.08),transparent)] blur-2xl"></div>
        </div>

        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
          <div class="mx-auto w-full max-w-md rounded-3xl border border-neutral-900 bg-neutral-950/60 backdrop-blur p-6 sm:p-8 shadow-[0_10px_40px_-20px_rgba(0,0,0,0.6)]">
            <h1 class="text-center text-2xl sm:text-3xl font-semibold tracking-tight">{{ __('auth.welcome_back') }}</h1>
            <p class="mt-1 text-center text-sm text-neutral-400">{{ __('auth.login_welcome') }}</p>

            @if (session('login_required'))
              <div class="mt-4 rounded-md border border-red-700 bg-red-900/30 text-red-300 p-3 text-sm">{{ session('login_required') }}</div>
            @endif
            @if (session('error_social_login'))
              <div class="mt-4 rounded-md border border-red-700 bg-red-900/30 text-red-300 p-3 text-sm">{{ __('general.error') }} "{{ session('error_social_login') }}"</div>
            @endif
            @include('errors.errors-forms')

            <form method="POST" action="{{ route('login') }}" id="formLoginRegister" class="mt-6 space-y-4" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="return" value="{{ count($errors) > 0 ? old('return') : url()->previous() }}">

              <div>
                <label for="username_email" class="block text-sm text-neutral-300">{{ __('auth.username_or_email') }}</label>
                <input id="username_email" name="username_email" type="text" required value="{{ old('username_email') }}"
                       class="mt-1 w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 text-sm outline-none focus:border-neutral-600" />
              </div>

              <div>
                <label for="password" class="block text-sm text-neutral-300">{{ __('auth.password') }}</label>
                <div class="mt-1 relative">
                  <input id="password" name="password" type="password" required
                         class="w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 pr-14 text-sm outline-none focus:border-neutral-600" />
                  <button type="button" id="togglePass" aria-label="Show password"
                          class="absolute inset-y-0 right-2 my-auto rounded-lg border border-neutral-800 px-2 text-xs text-neutral-300 hover:border-neutral-700">{{ __('general.show') }}</button>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-neutral-300">
                  <input type="checkbox" name="remember" class="h-4 w-4 rounded border-neutral-700 bg-neutral-950" {{ old('remember') ? 'checked' : '' }} />
                  {{ __('auth.remember_me') }}
                </label>
                <a class="text-sm text-neutral-300 hover:text-white" href="{{ url('password/reset') }}">{{ __('auth.forgot_password') }}</a>
              </div>

              @if ($settings->captcha == 'on')
                {!! NoCaptcha::displaySubmit('formLoginRegister', __('auth.login'), ['data-size' => 'invisible', 'id' => 'btnLoginRegister', 'class' => 'w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200']) !!}
                {!! NoCaptcha::renderJs() !!}
              @else
                <button id="btnLoginRegister" type="submit" class="w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">{{ __('auth.login') }}</button>
              @endif
            </form>

            <div id="errorLogin" role="alert" aria-live="assertive" class="hidden mt-3 rounded-md border border-red-700 bg-red-900/30 text-red-300 p-3 text-sm">
              <ul id="showErrorsLogin" class="list-disc pl-5"></ul>
            </div>

            @if ($settings->captcha == 'on')
              <small class="btn-block text-center mt-3">{{__('auth.protected_recaptcha')}} <a href="https://policies.google.com/privacy" target="_blank">{{__('general.privacy')}}</a> - <a href="https://policies.google.com/terms" target="_blank">{{__('general.terms')}}</a></small>
            @endif

            @if ($settings->registration_active == '1')
              <p class="mt-4 text-center text-sm text-neutral-400">{{ __('auth.not_have_account') }} <a class="underline hover:text-white" href="{{ route('register') }}">{{ __('auth.sign_up') }}</a></p>
            @endif
          </div>
        </section>
      </div>
    </main>

    <script>
      const toggle = document.getElementById('togglePass');
      const input  = document.getElementById('password');
      if (toggle && input) {
        toggle.addEventListener('click', () => {
          const show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          toggle.textContent = show ? '{{ __('general.hide') }}' : '{{ __('general.show') }}';
          toggle.setAttribute('aria-label', show ? '{{ __('general.hide') }}' : '{{ __('general.show') }}');
        });
      }
      // AJAX submit so controller accepts JSON and avoids 404
      const form = document.getElementById('formLoginRegister');
      if (form) {
        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const btn = document.getElementById('btnLoginRegister');
          if (btn) btn.setAttribute('disabled', 'true');
          const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: new FormData(form)
          });
          let data;
          try { data = await res.json(); } catch (_) { data = null; }
          const errBox = document.getElementById('errorLogin');
          const errList = document.getElementById('showErrorsLogin');
          if (!res.ok && errBox && errList) {
            errList.innerHTML = `<li>{{ __('auth.failed') }}</li>`;
            errBox.classList.remove('hidden');
            document.getElementById('username_email')?.focus();
            if (btn) btn.removeAttribute('disabled');
            return;
          }
          if (data && data.success) {
            const url = (data.url_return) ? data.url_return : '{{ url('/') }}';
            window.location.href = url;
            return;
          }
          if (data && data.check_account) {
            // Email verification flow
            const el = document.getElementById('checkAccount');
            if (el) { el.textContent = data.check_account; el.classList.remove('display-none'); }
          }
          if (data && data.errors && errBox && errList) {
            errList.innerHTML = Object.values(data.errors).map(m => `<li>${m}</li>`).join('');
            errBox.classList.remove('hidden');
            document.getElementById('username_email')?.focus();
          }
          if (window.grecaptcha) { try { grecaptcha.reset(); } catch(_){} }
          if (btn) btn.removeAttribute('disabled');
        });
      }
    </script>
  </body>
 </html>


