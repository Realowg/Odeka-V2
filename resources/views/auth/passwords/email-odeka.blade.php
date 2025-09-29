<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('auth.password_recover') }} — {{ config('settings.title') }}</title>
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
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
      <div class="mx-auto w-full max-w-md rounded-3xl border border-neutral-900 bg-neutral-950/60 backdrop-blur p-6 sm:p-8">
        <h1 class="text-center text-2xl sm:text-3xl font-semibold tracking-tight">{{ __('auth.password_recover') }}</h1>
        <p class="mt-1 text-center text-sm text-neutral-400">{{ __('auth.recover_pass_subtitle') }}</p>

        @include('errors.errors-forms')
        @if (session('status'))
          <div class="mt-4 rounded-md border border-emerald-700 bg-emerald-900/30 text-emerald-300 p-3 text-sm">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="passwordEmailForm" class="mt-6 space-y-4">
          @csrf
          <div>
            <label for="email" class="block text-sm text-neutral-300">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-neutral-800 bg-neutral-950 px-4 py-3 text-sm outline-none focus:border-neutral-600" />
          </div>
          @if ($settings->captcha == 'on')
            {!! NoCaptcha::displaySubmit('passwordEmailForm', __('auth.send_pass_reset'), ['data-size' => 'invisible', 'class' => 'w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200']) !!}
            {!! NoCaptcha::renderJs() !!}
          @else
            <button type="submit" class="w-full rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">{{ __('auth.send_pass_reset') }}</button>
          @endif
        </form>
      </div>
    </main>
  </body>
 </html>


