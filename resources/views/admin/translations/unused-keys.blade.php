@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
  <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <a class="text-reset" href="{{ route('translations') }}">Translations</a>
  <i class="bi-chevron-right me-1 fs-6"></i>
  <span class="text-muted">Unused Keys</span>
</h5>

<div class="content">
  <div class="row">
    <div class="col-lg-12">

      @if(count($unusedKeys) > 0)
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> Found {{ count($unusedKeys) }} potentially unused translation keys. Consider removing them to keep your database clean.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">
          <h5 class="mb-3">Unused Translation Keys</h5>
          <p class="text-muted">These keys exist in the database but were not found in any Blade files or controllers.</p>

          @if(count($unusedKeys) > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th width="120">Group</th>
                  <th width="200">Key</th>
                  <th>Full Key</th>
                  <th width="100">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($unusedKeys as $key)
                <tr id="key-row-{{ md5($key['full_key']) }}">
                  <td><span class="badge bg-secondary">{{ $key['group'] }}</span></td>
                  <td><code>{{ $key['key'] }}</code></td>
                  <td><code class="text-warning">{{ $key['full_key'] }}</code></td>
                  <td>
                    <button 
                      class="btn btn-sm btn-danger" 
                      onclick="deleteUnusedKey('{{ $key['group'] }}', '{{ $key['key'] }}')"
                      title="Delete this key from all locales">
                      <i class="bi-trash"></i> Delete
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="alert alert-info mt-4">
            <strong><i class="bi-info-circle"></i> Note:</strong> This scan checks Blade files and controllers. Keys may still be used in:
            <ul class="mb-0">
              <li>JavaScript files</li>
              <li>API responses</li>
              <li>Email templates</li>
              <li>Dynamically generated keys</li>
            </ul>
            Please verify before deleting!
          </div>
          @else
          <div class="alert alert-success">
            <i class="bi-check-circle"></i> Great! No unused keys found. All your translations are being used.
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

@section('javascript')
<script>
function deleteUnusedKey(group, key) {
  if (!confirm(`Are you sure you want to delete "${group}.${key}" from all locales?\n\nThis action cannot be undone.`)) {
    return;
  }

  // Find all translations with this group and key
  fetch('{{ route("translations") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      action: 'delete_by_key',
      group: group,
      key: key
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Remove row from table
      const rowId = 'key-row-' + MD5(group + '.' + key);
      document.getElementById(rowId)?.remove();
      
      // Show success message
      alert('Translation key deleted successfully');
    } else {
      alert('Error deleting translation key');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error deleting translation key');
  });
}

// Simple MD5 hash for row ID
function MD5(string) {
  return string.split('').reduce((a,b)=>{a=((a<<5)-a)+b.charCodeAt(0);return a&a},0).toString(16);
}
</script>
@endsection
