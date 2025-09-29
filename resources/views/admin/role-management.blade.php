@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Role Management</h1>
  <div>
    @if (auth()->id() === 1)
      <form method="post" action="{{ url('panel/admin/role-management/migrate') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-primary" @if(!$hasUserRoles) disabled @endif>Migrate Admin Users</button>
      </form>
    @endif
    <a href="{{ url('panel/admin/role-management/stats') }}" class="btn btn-sm btn-outline-secondary @if(!$hasUserRoles) disabled @endif">View Stats (JSON)</a>
  </div>
  </div>

@if (session('success_message'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi-check2 me-1"></i> {{ session('success_message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if (session('error_message'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi-exclamation-triangle me-1"></i> {{ session('error_message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card mb-4">
  <div class="card-header">Assign Role</div>
  <div class="card-body">
    <form id="assignForm" method="post" action="{{ url('panel/admin/role-management/assign') }}" class="row g-3 align-items-end">
      @csrf
      <div class="col-md-4">
        <label class="form-label">Admin User</label>
        <select class="form-select" id="adminUserSelect" name="user_id" required>
          @foreach(\App\Models\User::where('role','admin')->orderBy('username')->get() as $admin)
            <option value="{{ $admin->id }}">{{ $admin->username }} (ID: {{ $admin->id }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Role</label>
        <select class="form-select" id="roleNameSelect" name="role_name" required>
          @foreach(array_keys(\App\Models\UserRole::getRolePermissions()) as $role)
            <option value="{{ $role }}">{{ ucfirst(str_replace('_',' ', $role)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Custom Permissions (optional)</label>
        <select class="form-select" id="customPermissionsSelect" name="custom_permissions[]" multiple>
          @foreach(\App\Models\UserRole::getAllPermissions() as $perm)
            <option value="{{ $perm }}">{{ $perm }}</option>
          @endforeach
        </select>
        <small class="text-muted">Hold Cmd/Ctrl to select multiple</small>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-success w-100" @if(!$hasUserRoles) disabled @endif>Assign</button>
      </div>
    </form>

    <div id="effectivePanel" class="mt-4 p-3 border rounded" style="display:none;">
      <div class="fw-semibold mb-2">Permissions Overview</div>
      <div class="row small">
        <div class="col-md-3"><span class="text-muted">Current Role:</span> <span id="effRole">—</span></div>
        <div class="col-md-9"><span class="text-muted">Current Role Defaults:</span> <span id="effDefaults">—</span></div>
      </div>
      <div class="row small mt-1">
        <div class="col-md-12"><span class="text-muted">Legacy:</span> <span id="effLegacy">—</span></div>
      </div>
      <div class="row small mt-1">
        <div class="col-md-12"><span class="text-muted">Effective:</span> <span id="effEffective">—</span></div>
      </div>
      <div class="row small mt-1">
        <div class="col-md-12"><span class="text-muted">Selected Role Defaults (based on dropdown):</span> <span id="effSelectedDefaults">—</span></div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">Admin Users</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0 align-middle">
        <thead>
          <tr>
            <th>User</th>
            <th>Current Enhanced Role</th>
            <th>Custom Permissions</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($adminUsers as $user)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <img src="{{ \App\Helper::getFile(config('path.avatar').$user->avatar) }}" class="rounded-circle me-2" width="32" height="32" />
                  <div>
                    <div class="fw-semibold">{{ $user->username }}</div>
                    <div class="text-muted small">ID: {{ $user->id }}</div>
                  </div>
                </div>
              </td>
              <td>
                @if ($hasUserRoles && $user->userRole)
                  <span class="badge bg-primary">{{ ucfirst(str_replace('_',' ', $user->userRole->role_name)) }}</span>
                @else
                  <span class="text-muted">Legacy</span>
                @endif
              </td>
              <td>
                @if ($hasUserRoles && $user->userRole && !empty($user->userRole->permissions))
                  @foreach($user->userRole->permissions as $perm)
                    <span class="badge text-bg-light border me-1">{{ $perm }}</span>
                  @endforeach
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary js-view-perms" data-user="{{ $user->id }}">View</button>
                  <form method="post" action="{{ url('panel/admin/role-management/remove') }}" onsubmit="return confirm('Remove enhanced role for this user?');">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}" />
                    <button type="submit" class="btn btn-sm btn-outline-danger" @if($user->id === 1 || !$hasUserRoles) disabled @endif>Remove</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">
    {{ $adminUsers->links() }}
  </div>
</div>

@section('javascript')
<script>
(function(){
  const $user = document.getElementById('adminUserSelect');
  const $role = document.getElementById('roleNameSelect');
  const $custom = document.getElementById('customPermissionsSelect');
  const panel = document.getElementById('effectivePanel');
  const effRole = document.getElementById('effRole');
  const effDefaults = document.getElementById('effDefaults');
  const effLegacy = document.getElementById('effLegacy');
  const effEffective = document.getElementById('effEffective');
  const effSelectedDefaults = document.getElementById('effSelectedDefaults');

  // Role permission map from server
  const rolePermissions = @json(\App\Models\UserRole::getRolePermissions());

  function updateSelectedDefaults(){
    const selected = ($role && $role.value) ? $role.value : null;
    const list = selected && rolePermissions[selected] ? rolePermissions[selected] : [];
    effSelectedDefaults.textContent = (list || []).join(', ') || '—';
  }

  async function fetchEffective(userId){
    try {
      const url = `{{ url('panel/admin/role-management/effective-permissions') }}?user_id=${encodeURIComponent(userId)}`;
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      if (!res.ok) return;
      const data = await res.json();
      panel.style.display = 'block';
      effRole.textContent = data.role || '—';
      effDefaults.textContent = (data.role_defaults||[]).join(', ') || '—';
      effLegacy.textContent = (data.legacy||[]).join(', ') || '—';
      effEffective.textContent = (data.effective||[]).join(', ') || '—';
      updateSelectedDefaults();

      // Preselect custom permissions if available
      if (Array.isArray(data.custom)) {
        [...$custom.options].forEach(opt => {
          opt.selected = data.custom.includes(opt.value);
        });
      }

      // If role from server differs from current selection, keep current user choice
      // but you can uncomment to sync role select:
      // if (data.role) $role.value = data.role;
    } catch(e) {
      // silent
    }
  }

  if ($user) {
    fetchEffective($user.value);
    $user.addEventListener('change', () => fetchEffective($user.value));
  }

  if ($role) {
    updateSelectedDefaults();
    $role.addEventListener('change', updateSelectedDefaults);
  }

  // Table "View" buttons
  document.querySelectorAll('.js-view-perms').forEach(btn => {
    btn.addEventListener('click', () => {
      fetchEffective(btn.getAttribute('data-user'));
      window.scrollTo({ top: document.getElementById('assignForm').offsetTop - 20, behavior: 'smooth' });
    });
  });
})();
</script>
@endsection

@endsection


