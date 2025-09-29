<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('auth.sign_up') }} — {{ config('settings.title') }}</title>
    <script>window.tailwind={config:{corePlugins:{preflight:false}}};</script>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-neutral-950 text-neutral-100">
    <header class="sticky top-0 z-40 backdrop-blur border-b border-neutral-900/60">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/'.($settings->logo ?? '')) }}" alt="Logo" class="h-7 w-auto" />
            <span class="font-semibold tracking-tight">{{ config('settings.title') }}</span>
          </a>
          <nav class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="inline-flex rounded-full border border-neutral-800 px-4 py-2 text-sm hover:border-neutral-700">{{ __('auth.sign_in') }}</a>
          </nav>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
      <div class="mx-auto w-full max-w-md rounded-3xl border border-neutral-900 bg-neutral-950/60 backdrop-blur p-6 sm:p-8">
        <h1 class="text-center text-2xl sm:text-3xl font-semibold tracking-tight">{{ __('auth.sign_up') }}</h1>
        <p class="mt-1 text-center text-sm text-neutral-400">{{ __('auth.signup_welcome') }}</p>

        @include('errors.errors-forms')

        <form method="POST" action="{{ route('register') }}" id="formLoginRegister" class="mt-6 space-y-4">
          @csrf
          <div>
            <label for="name" class="block text-sm text-neutral-300">{{ __('auth.full_name') }}</label>
            <input id="name" name="name" required value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 text-sm outline-none focus:border-neutral-600" />
          </div>
          <div>
            <label for="email" class="block text-sm text-neutral-300">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 text-sm outline-none focus:border-neutral-600" />
          </div>
          <div>
            <label for="password" class="block text-sm text-neutral-300">{{ __('auth.password') }}</label>
            <input id="password" name="password" type="password" required class="mt-1 w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 text-sm outline-none focus:border-neutral-600" />
          </div>

          <label class="inline-flex items-start gap-2 text-sm text-neutral-300">
            <input type="checkbox" name="agree_gdpr" class="mt-1 h-4 w-4 rounded border-neutral-700 bg-neutral-950" required />
            <span>
              {{__('admin.i_agree_gdpr')}}
              <a href="{{$settings->link_terms}}" target="_blank">{{__('admin.terms_conditions')}}</a>
              {{ __('general.and') }}
              <a href="{{$settings->link_privacy}}" target="_blank">{{__('admin.privacy_policy')}}</a>
            </span>
          </label>

          <div class="hidden rounded-md border border-red-700 bg-red-900/30 text-red-300 p-3 text-sm" id="errorLogin">
            <ul id="showErrorsLogin" class="list-disc pl-5 space-y-1"></ul>
          </div>
          <div class="hidden rounded-md border border-emerald-700 bg-emerald-900/30 text-emerald-300 p-3 text-sm" id="checkAccount"></div>

          @if ($settings->captcha == 'on')
            {!! NoCaptcha::displaySubmit('formLoginRegister', __('auth.sign_up'), ['data-size' => 'invisible', 'id' => 'btnLoginRegister', 'class' => 'w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200']) !!}
            {!! NoCaptcha::renderJs() !!}
          @else
            <button id="btnLoginRegister" type="submit" class="w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">{{ __('auth.sign_up') }}</button>
          @endif
        </form>

        <p class="mt-6 text-center text-sm text-neutral-400">
          {{ __('auth.already_have_an_account') }}
          <a class="underline hover:text-white" href="{{ route('login') }}">{{ __('auth.sign_in') }}</a>
        </p>
      </div>
    </main>
    <script>
      const form = document.getElementById('formLoginRegister');
      if (form) {
        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const btn = document.getElementById('btnLoginRegister');
          if (btn) btn.setAttribute('disabled', 'true');
          const res = await fetch(form.action, { method: 'POST', headers: { 'Accept': 'application/json' }, body: new FormData(form) });
          let data; try { data = await res.json(); } catch(_) { data = null; }
          const errBox = document.getElementById('errorLogin');
          const errList = document.getElementById('showErrorsLogin');
          const okBox  = document.getElementById('checkAccount');
          if (okBox) okBox.classList.add('hidden');
          if (errBox) errBox.classList.add('hidden');

          if (data && data.success) {
            if (data.check_account) {
              if (okBox) { okBox.textContent = data.check_account; okBox.classList.remove('hidden'); }
              if (btn) btn.removeAttribute('disabled');
              if (window.grecaptcha) { try { grecaptcha.reset(); } catch(_){} }
              return;
            }
            const url = (data.url_return) ? data.url_return : '{{ url('/') }}';
            window.location.href = url;
            return;
          }
          if (data && data.errors && errBox && errList) {
            errList.innerHTML = Object.values(data.errors).flat().map(m => `<li>${m}</li>`).join('');
            errBox.classList.remove('hidden');
          }
          if (window.grecaptcha) { try { grecaptcha.reset(); } catch(_){} }
          if (btn) btn.removeAttribute('disabled');
        });
      }
    </script>
  </body>
 </html>


