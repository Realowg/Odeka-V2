<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Submit a Brief</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-neutral-950 text-neutral-100">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
      <h1 class="text-2xl font-semibold">Submit a Campaign Brief</h1>
      @if(session('success_message'))
        <div class="mt-4 rounded-md bg-green-900/30 border border-green-700 text-green-300 p-3">{{ session('success_message') }}</div>
      @endif
      @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-900/30 border border-red-700 text-red-300 p-3">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <form method="POST" action="{{ url('brief') }}" class="mt-6 space-y-4">
        @csrf
        <div>
          <label class="block text-sm text-neutral-300">Company</label>
          <input name="company" class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2" />
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-neutral-300">Name *</label>
            <input name="name" required class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm text-neutral-300">Email *</label>
            <input type="email" name="email" required class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2" />
          </div>
        </div>
        <div>
          <label class="block text-sm text-neutral-300">Phone</label>
          <input name="phone" class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm text-neutral-300">Objectives</label>
          <textarea name="objectives" rows="4" class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2"></textarea>
        </div>
        <div>
          <label class="block text-sm text-neutral-300">Budget</label>
          <input name="budget" class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm text-neutral-300">Notes</label>
          <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-neutral-800 bg-neutral-900 px-3 py-2"></textarea>
        </div>
        <div class="pt-2">
          <button class="rounded-full bg-white text-neutral-900 px-5 py-2 text-sm font-medium hover:bg-neutral-200">Send</button>
        </div>
      </form>
    </div>
  </body>
 </html>


