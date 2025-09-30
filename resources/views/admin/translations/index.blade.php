@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
  <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <span class="text-muted">Translations</span>

  <div class="btn-group float-lg-end mt-1 mt-lg-0" role="group">
    <a href="{{ route('translations.import') }}" class="btn btn-sm btn-primary">
      <i class="bi-upload"></i> Import
    </a>
    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
      <i class="bi-download"></i> Export
    </button>
    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exportForTranslationModal">
      <i class="bi-translate"></i> Export for Translation
    </button>
    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#syncModal">
      <i class="bi-arrow-repeat"></i> Sync from Files
    </button>
    <form method="POST" action="{{ url('panel/admin/translations/clear-cache') }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Clear all translation caches? This will force reload all translations from database.')">
        <i class="bi-lightning"></i> Clear Cache
      </button>
    </form>
    <a href="{{ route('translations.scan') }}" class="btn btn-sm btn-warning">
      <i class="bi-search"></i> Scan Keys
    </a>
    <a href="{{ route('translations.unused') }}" class="btn btn-sm btn-danger">
      <i class="bi-trash"></i> Find Unused
    </a>
  </div>
</h5>

<div class="content">
  <div class="row">
    <div class="col-lg-12">

      @if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if (session('error_message'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if (session('import_result'))
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Import Details:</strong>
        <ul class="mb-0 mt-2">
          <li>Created: {{ session('import_result')['created'] }}</li>
          <li>Updated: {{ session('import_result')['updated'] }}</li>
          <li>Skipped: {{ session('import_result')['skipped'] }}</li>
          @if(!empty(session('import_result')['errors']))
            <li class="text-danger">Errors: {{ count(session('import_result')['errors']) }}</li>
          @endif
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">Total Translations</h6>
              <h3 class="mb-0">{{ number_format($stats['total_translations']) }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">Languages</h6>
              <h3 class="mb-0">{{ count($stats['locales']) }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">Groups</h6>
              <h3 class="mb-0">{{ count($stats['groups']) }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">By Locale</h6>
              <div class="small">
                @foreach($stats['by_locale'] as $locale => $count)
                  <div>{{ strtoupper($locale) }}: {{ $count }}</div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">

          <!-- Filters -->
          <form method="GET" action="{{ route('translations') }}" class="mb-4">
            <div class="row g-3">
              <div class="col-md-2">
                <label class="form-label">Language</label>
                <select name="locale" class="form-select">
                  <option value="">All Languages</option>
                  @foreach($locales as $locale)
                    <option value="{{ $locale }}" {{ request('locale') === $locale ? 'selected' : '' }}>
                      {{ strtoupper($locale) }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Group</label>
                <select name="group" class="form-select">
                  <option value="">All Groups</option>
                  @foreach($groups as $group)
                    <option value="{{ $group }}" {{ request('group') === $group ? 'selected' : '' }}>
                      {{ $group }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search key or value..." value="{{ request('search') }}">
              </div>
              <div class="col-md-2">
                <label class="form-label">Per Page</label>
                <select name="per_page" class="form-select">
                  <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                  <option value="50" {{ request('per_page') == 50 || !request('per_page') ? 'selected' : '' }}>50</option>
                  <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi-search"></i> Filter
                  </button>
                </div>
              </div>
            </div>
          </form>

          <!-- Translations Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th width="80">Lang</th>
                  <th width="120">Group</th>
                  <th width="200">Key</th>
                  <th>Value</th>
                  <th width="120">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($translations as $translation)
                <tr id="row-{{ $translation->id }}">
                  <td><span class="badge bg-primary">{{ strtoupper($translation->locale) }}</span></td>
                  <td><span class="badge bg-secondary">{{ $translation->group }}</span></td>
                  <td><code class="small">{{ $translation->key }}</code></td>
                  <td>
                    <div class="translation-value" id="value-{{ $translation->id }}">
                      {{ Str::limit($translation->value, 100) }}
                    </div>
                    <div class="translation-edit d-none" id="edit-{{ $translation->id }}">
                      <textarea class="form-control form-control-sm" rows="2">{{ $translation->value }}</textarea>
                      <div class="mt-2">
                        <button class="btn btn-sm btn-success" onclick="saveTranslation({{ $translation->id }})">Save</button>
                        <button class="btn btn-sm btn-secondary" onclick="cancelEdit({{ $translation->id }})">Cancel</button>
                      </div>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-primary" onclick="editTranslation({{ $translation->id }})" title="Edit">
                      <i class="bi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ url('panel/admin/translations', $translation->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                        <i class="bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    No translations found. <a href="{{ route('translations.import') }}">Import translations</a> or <a href="#" data-bs-toggle="modal" data-bs-target="#syncModal">sync from files</a>.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4">
            {{ $translations->appends(request()->except('page'))->links() }}
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('translations.export') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Export Translations</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Format</label>
            <select name="format" class="form-select" required>
              <option value="csv">CSV</option>
              <option value="json">JSON</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Languages (optional)</label>
            <select name="locale[]" class="form-select" multiple>
              @foreach($locales as $locale)
                <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
              @endforeach
            </select>
            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Groups (optional)</label>
            <select name="group[]" class="form-select" multiple>
              @foreach($groups as $group)
                <option value="{{ $group }}">{{ $group }}</option>
              @endforeach
            </select>
            <small class="text-muted">Leave empty to export all</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('translations.sync') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Sync from Files</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Import translations from PHP language files in <code>lang/</code> directory to database.</p>
          <div class="alert alert-warning">
            <i class="bi-exclamation-triangle"></i> Existing translations will not be overwritten.
          </div>
          <div class="mb-3">
            <label class="form-label">Language (optional)</label>
            <select name="locale" class="form-select">
              <option value="">All Languages</option>
              @foreach($locales as $locale)
                <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Group (optional)</label>
            <select name="group" class="form-select">
              <option value="">All Groups</option>
              @foreach($groups as $group)
                <option value="{{ $group }}">{{ $group }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info">Sync Now</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Export for Translation Modal -->
<div class="modal fade" id="exportForTranslationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('translations.export.fortranslation') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Export for Translation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Export all keys from a source language with empty target values for translation.</p>
          
          <div class="mb-3">
            <label class="form-label">Source Language (e.g., English)</label>
            <select name="source_locale" class="form-select" required>
              @foreach($locales as $locale)
                <option value="{{ $locale }}" {{ $locale === 'en' ? 'selected' : '' }}>
                  {{ strtoupper($locale) }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">The language containing the values to be translated</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Target Language (for translation)</label>
            <select name="target_locale" class="form-select" required>
              @foreach($locales as $locale)
                <option value="{{ $locale }}" {{ $locale === 'fr' ? 'selected' : '' }}>
                  {{ strtoupper($locale) }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">The language you want to translate into</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Format</label>
            <select name="format" class="form-select" required>
              <option value="csv">CSV</option>
              <option value="json">JSON</option>
            </select>
          </div>

          <div class="alert alert-info">
            <strong>How it works:</strong>
            <ol class="mb-0 mt-2">
              <li>Export generates a file with source values and empty target fields</li>
              <li>Fill in translations in the "value" column</li>
              <li>Import the file back to add translations to database</li>
            </ol>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Export for Translation</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('javascript')
<script>
function editTranslation(id) {
  document.getElementById('value-' + id).classList.add('d-none');
  document.getElementById('edit-' + id).classList.remove('d-none');
}

function cancelEdit(id) {
  document.getElementById('value-' + id).classList.remove('d-none');
  document.getElementById('edit-' + id).classList.add('d-none');
}

function saveTranslation(id) {
  const textarea = document.querySelector('#edit-' + id + ' textarea');
  const value = textarea.value;
  
  fetch('{{ url("panel/admin/translations") }}/' + id, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ 
      _method: 'PUT',
      value: value 
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('HTTP error! status: ' + response.status);
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      document.getElementById('value-' + id).textContent = value.substring(0, 100) + (value.length > 100 ? '...' : '');
      cancelEdit(id);
      
      // Show success message
      const alert = document.createElement('div');
      alert.className = 'alert alert-success alert-dismissible fade show';
      alert.innerHTML = '<i class="bi bi-check2 me-1"></i> Translation updated successfully <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
      document.querySelector('.content .row .col-lg-12').insertBefore(alert, document.querySelector('.row .col-lg-12 > .card:first-child'));
      setTimeout(() => alert.remove(), 3000);
    } else {
      throw new Error(data.message || 'Update failed');
    }
  })
  .catch(error => {
    alert('Error updating translation: ' + error.message);
    console.error('Error:', error);
  });
}
</script>
@endsection
