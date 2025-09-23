<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Channel</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-neutral-950 text-neutral-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
      <h1 class="text-2xl font-semibold">O'Channel</h1>
      <p class="text-neutral-400 mt-2">This is a placeholder page. Real content can be added later.</p>
      <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach((array)($shows ?? []) as $s)
          <div class="rounded-2xl border border-neutral-800">
            <div class="aspect-video bg-neutral-900"></div>
            <div class="p-4">
              <div class="font-medium">{{ $s['name'] ?? 'Show' }}</div>
              <div class="text-sm text-neutral-400">{{ $s['tag'] ?? '' }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </body>
 </html>


