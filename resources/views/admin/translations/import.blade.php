@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
  <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <a class="text-reset" href="{{ route('translations') }}">Translations</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <span class="text-muted">Import</span>
</h5>

<div class="content">
  <div class="row">
    <div class="col-lg-8 offset-lg-2">

      @if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if (session('error_message'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if (session('import_result'))
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h5 class="alert-heading">Import Results</h5>
        <ul class="mb-0">
          <li><strong>Created:</strong> {{ session('import_result')['created'] }} new translations</li>
          <li><strong>Updated:</strong> {{ session('import_result')['updated'] }} existing translations</li>
          <li><strong>Skipped:</strong> {{ session('import_result')['skipped'] }} translations</li>
          @if(!empty(session('import_result')['errors']))
            <li class="text-danger"><strong>Errors:</strong> {{ count(session('import_result')['errors']) }} errors</li>
            <details class="mt-2">
              <summary class="text-danger" style="cursor: pointer;">View errors</summary>
              <ul class="mt-2">
                @foreach(session('import_result')['errors'] as $error)
                  <li class="small">{{ $error }}</li>
                @endforeach
              </ul>
            </details>
          @endif
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <div class="card shadow-custom border-0">
        <div class="card-body p-lg-5">
          <h5 class="mb-4">Import Translations</h5>

          <form method="POST" action="{{ route('translations.import') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
              <label class="form-label fw-bold">Upload File</label>
              <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".csv,.json,.txt" required>
              @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">
                Supported formats: CSV, JSON (max 10MB)
              </small>
            </div>

            <div class="mb-4">
              <label class="form-label fw-bold">Import Mode</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode" id="mode_merge" value="merge" checked>
                <label class="form-check-label" for="mode_merge">
                  <strong>Merge</strong> - Update existing translations and add new ones
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode" id="mode_overwrite" value="overwrite">
                <label class="form-check-label" for="mode_overwrite">
                  <strong>Overwrite</strong> - Replace all matching translations
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode" id="mode_add" value="add_only">
                <label class="form-check-label" for="mode_add">
                  <strong>Add Only</strong> - Skip existing translations, only add new ones
                </label>
              </div>
            </div>

            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="dry_run" id="dry_run" value="1">
                <label class="form-check-label" for="dry_run">
                  <strong>Preview mode</strong> (Dry run) - Show what would be imported without saving
                </label>
              </div>
            </div>

            <div class="alert alert-info">
              <h6 class="alert-heading"><i class="bi-info-circle"></i> File Format</h6>
              
              <p><strong>CSV Format:</strong></p>
              <pre class="mb-0"><code>locale,group,key,value
en,odeka,hero_headline,"We create, distribute..."
fr,odeka,hero_headline,"Nous créons..."</code></pre>

              <p class="mt-3"><strong>JSON Format:</strong></p>
              <pre class="mb-0"><code>{
  "en": {
    "odeka": {
      "hero_headline": "We create..."
    }
  }
}</code></pre>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <a href="{{ route('translations.sample.csv') }}" class="btn btn-outline-secondary btn-sm w-100">
                  <i class="bi-download"></i> Download Sample CSV
                </a>
              </div>
              <div class="col-md-6">
                <a href="{{ route('translations.sample.json') }}" class="btn btn-outline-secondary btn-sm w-100">
                  <i class="bi-download"></i> Download Sample JSON
                </a>
              </div>
            </div>

            <div class="d-flex gap-2">
              <a href="{{ route('translations') }}" class="btn btn-secondary">
                <i class="bi-arrow-left"></i> Back
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi-upload"></i> Import Translations
              </button>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>
@endsection
