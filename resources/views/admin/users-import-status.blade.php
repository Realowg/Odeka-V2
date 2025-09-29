@extends('admin.layout')

@section('content')
<div class="content d-flex flex-column flex-column-fluid">
  <div class="container-fluid">
    <h4 class="mb-4">User Import Status</h4>
    <div class="card card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ $import->status }}</dd>
        <dt class="col-sm-3">File</dt>
        <dd class="col-sm-9">{{ $import->filename }}</dd>
        <dt class="col-sm-3">Counts</dt>
        <dd class="col-sm-9">Total: {{ $import->total_rows }}, Created: {{ $import->created_count }}, Updated: {{ $import->updated_count }}, Skipped: {{ $import->skipped_count }}, Failed: {{ $import->failed_count }}</dd>
      </dl>
      <div class="mt-3">
        @if ($import->errors_csv_path)
          <a class="btn btn-outline-danger btn-sm" href="{{ url('panel/admin/users/import/'.$import->id.'/errors.csv') }}">Download errors CSV</a>
        @endif
        @if ($import->summary_json_path)
          <a class="btn btn-outline-secondary btn-sm" href="{{ url('panel/admin/users/import/'.$import->id.'/summary.json') }}">Download summary JSON</a>
        @endif
        <a class="btn btn-link btn-sm" href="{{ url('panel/admin/users/import') }}">New import</a>
      </div>
    </div>
  </div>
</div>
@endsection


