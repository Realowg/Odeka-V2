@extends('admin.layout')

@section('content')
<div class="content d-flex flex-column flex-column-fluid">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Import Users</h4>
      <a href="{{ url('panel/admin/users/import/sample') }}" class="btn btn-sm btn-dark">Download sample CSV</a>
    </div>

    @include('errors.errors-forms')

    <form action="{{ url('panel/admin/users/import') }}" method="POST" enctype="multipart/form-data" class="card card-body">
      @csrf
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">CSV file</label>
          <input type="file" name="file" class="form-control" accept=".csv,text/csv" required>
          <small class="text-muted">Max 10 MB. First row should be headers.</small>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Default role (optional)</label>
          <input type="text" name="default_role" class="form-control" placeholder="member">
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-end">
          <div class="form-check me-4">
            <input class="form-check-input" type="checkbox" name="update_existing" id="update_existing">
            <label class="form-check-label" for="update_existing">Update existing (by email)</label>
          </div>
          <div class="form-check me-4">
            <input class="form-check-input" type="checkbox" name="send_invite" id="send_invite" checked>
            <label class="form-check-label" for="send_invite">Send invite if no password</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dry_run" id="dry_run">
            <label class="form-check-label" for="dry_run">Validate only (dry‑run)</label>
          </div>
        </div>
      </div>
      <button class="btn btn-primary">Start import</button>
    </form>
  </div>
</div>
@endsection


