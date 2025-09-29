<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUserImport;
use App\Models\UserImport;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserImportController extends Controller
{
    public function form()
    {
        $roles = array_keys(UserRole::getRolePermissions());
        return view('admin.users-import', [
            'roles' => $roles,
        ]);
    }

    public function sample(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_sample.csv"',
        ];
        $callback = function () {
            $out = fopen('php://output', 'w');
            // Columns no longer include send_invite or role; role can be set via the form dropdown
            fputcsv($out, ['email','username','name','status','language','preferred_currency','password']);
            fputcsv($out, ['jane@example.com','jane','Jane Doe','active','en','EUR','']);
            fputcsv($out, ['john@example.com','johnny','John Smith','active','fr','XOF','MyS3cur3Pwd']);
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function upload(Request $request)
    {
        // Normalize checkbox values prior to validation to avoid boolean rule failing on "on"/missing
        $request->merge([
            'update_existing' => $request->has('update_existing'),
            'send_invite' => $request->has('send_invite'),
            'dry_run' => $request->has('dry_run'),
        ]);

        $roles = array_keys(UserRole::getRolePermissions());

        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,text/tsv,text/*,application/csv,application/vnd.ms-excel|max:10240',
            'default_role' => 'nullable|in:'.implode(',', $roles),
            'update_existing' => 'nullable|boolean',
            'send_invite' => 'nullable|boolean',
            'dry_run' => 'nullable|boolean',
        ]);

        $path = $request->file('file')->store('imports');

        $import = UserImport::create([
            'admin_id' => auth()->id(),
            'filename' => $request->file('file')->getClientOriginalName(),
            'storage_path' => $path,
            'options' => [
                'default_role' => $request->input('default_role') ?: null,
                'update_existing' => (bool) $request->input('update_existing'),
                'send_invite' => (bool) $request->input('send_invite'),
                'dry_run' => (bool) $request->input('dry_run'),
            ],
            'status' => 'queued',
        ]);

        ProcessUserImport::dispatch($import);

        return redirect('panel/admin/users/import/'.$import->id);
    }

    public function status(UserImport $import)
    {
        return view('admin.users-import-status', [
            'import' => $import,
        ]);
    }

    public function errorsCsv(UserImport $import)
    {
        abort_unless($import->errors_csv_path, 404);
        return Storage::download($import->errors_csv_path);
    }

    public function summaryJson(UserImport $import)
    {
        abort_unless($import->summary_json_path, 404);
        return Storage::download($import->summary_json_path);
    }
}


