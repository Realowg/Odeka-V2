@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
  <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <a class="text-reset" href="{{ route('translations') }}">Translations</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <span class="text-muted">Scan Blade Files</span>
</h5>

<div class="content">
  <div class="row">
    <div class="col-lg-12">

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

      <!-- Missing Keys Alert -->
      @if(isset($missingKeys) && count($missingKeys) > 0)
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-1"></i> Missing Translations Detected</h6>
        <p>Found <strong>{{ count($missingKeys) }}</strong> missing translation keys in your database.</p>
        <form method="POST" action="{{ route('translations.import.missing') }}" class="mt-3">
          @csrf
          @foreach($missingKeys as $missingKey)
            <input type="hidden" name="keys[{{ $loop->index }}][locale]" value="{{ $missingKey['locale'] }}">
            <input type="hidden" name="keys[{{ $loop->index }}][group]" value="{{ $missingKey['group'] }}">
            <input type="hidden" name="keys[{{ $loop->index }}][key]" value="{{ $missingKey['key'] }}">
          @endforeach
          <button type="submit" class="btn btn-sm btn-warning">
            <i class="bi-download"></i> Import All Missing Keys to Database ({{ count($missingKeys) }})
          </button>
        </form>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">
          <h5 class="mb-3">Translation Keys Found in Blade Files</h5>
          <p class="text-muted">This scan found {{ count($keys) }} translation keys across your Blade templates.</p>

          @if(count($keys) > 0)
          <div class="alert alert-info">
            <i class="bi-info-circle"></i> These keys are currently used in your views. Make sure all of them have translations in the database.
          </div>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Group</th>
                  <th>Key</th>
                  <th>Full Key</th>
                  <th>Found In File</th>
                </tr>
              </thead>
              <tbody>
                @foreach($keys as $key)
                <tr>
                  <td><span class="badge bg-secondary">{{ $key['group'] }}</span></td>
                  <td><code>{{ $key['key'] }}</code></td>
                  <td><code>{{ $key['group'] }}.{{ $key['key'] }}</code></td>
                  <td class="small text-muted">{{ $key['file'] }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @else
          <div class="alert alert-warning">
            <i class="bi-exclamation-triangle"></i> No translation keys found in Blade files.
          </div>
          @endif

          <div class="mt-4">
            <a href="{{ route('translations') }}" class="btn btn-secondary">
              <i class="bi-arrow-left"></i> Back to Translations
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
