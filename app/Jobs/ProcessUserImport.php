<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserImport;
use App\Models\UserRole;
use App\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class ProcessUserImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public UserImport $import) {}

    public function handle(): void
    {
        $this->import->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $path = $this->import->storage_path;
        $options = $this->import->options ?? [];
        $sendInvite = (bool)($options['send_invite'] ?? false);
        $updateExisting = (bool)($options['update_existing'] ?? false);
        $dryRun = (bool)($options['dry_run'] ?? false);
        $defaultRole = $options['default_role'] ?? null;

        $stream = Storage::readStream($path);
        if (!$stream) {
            $this->import->update(['status' => 'failed', 'finished_at' => now()]);
            return;
        }

        $errors = fopen('php://temp', 'w+');
        // Enhanced error report for clarity when imports fail
        fputcsv($errors, ['row','email','type','column','value','message']);
        $writeErr = function(int $rowNum, string $email, string $type, string $column, $value, string $message) use ($errors) {
            if (is_array($value) || is_object($value)) { $value = json_encode($value); }
            fputcsv($errors, [$rowNum, $email, $type, $column, (string)$value, $message]);
        };

        // Detect delimiter (comma, semicolon, tab, pipe) and strip BOM if present
        $delimiters = [',',';','\t','|'];
        $delimiterUsed = ',';
        $normalize = function($col) {
            $col = preg_replace('/^\xEF\xBB\xBF/', '', (string) $col); // remove UTF-8 BOM
            return strtolower(trim($col));
        };

        $h = [];
        foreach ($delimiters as $candidate) {
            rewind($stream);
            $h = fgetcsv($stream, 0, $candidate);
            if ($h === false) { continue; }
            $norm = array_map($normalize, $h);
            if (in_array('email', $norm, true)) {
                $delimiterUsed = $candidate;
                break;
            }
        }
        if (empty($h)) { rewind($stream); $h = fgetcsv($stream, 0, $delimiterUsed); }
        $map = [];
        foreach ($h as $i => $col) { $map[$normalize($col)] = $i; }
        if (!array_key_exists('email', $map)) {
            // Cannot proceed reliably without email; report and stop cleanly
            $writeErr(1, '', 'failed', 'header', '', "Missing required column 'email' or wrong delimiter");
            // Save minimal reports and mark failed
            rewind($errors);
            $errorsPath = 'imports/errors_'.$this->import->id.'.csv';
            Storage::put($errorsPath, stream_get_contents($errors));
            fclose($errors);
            $summary = [
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'failed' => 0,
                'options' => [
                    'default_role' => $defaultRole,
                    'update_existing' => $updateExisting,
                    'send_invite' => $sendInvite,
                    'dry_run' => $dryRun,
                ],
                'file' => $this->import->filename,
            ];
            $summaryPath = 'imports/summary_'.$this->import->id.'.json';
            Storage::put($summaryPath, json_encode($summary));
            $this->import->update([
                'errors_csv_path' => $errorsPath,
                'summary_json_path' => $summaryPath,
                'status' => 'failed',
                'finished_at' => now(),
            ]);
            return;
        }

        $rowNum = 1; // header row
        $created = $updated = $skipped = $failed = 0; $total = 0;

        while (($row = fgetcsv($stream, 0, $delimiterUsed)) !== false) {
            $rowNum++; $total++;
            $getRaw = function(string $key) use ($map, $row) { $i = $map[$key] ?? null; return $i !== null ? (string)($row[$i] ?? '') : null; };
            $sanitize = function(?string $v) {
                if ($v === null) return null;
                // Strip BOM, non-breaking spaces and control chars, then trim
                $v = preg_replace('/^\xEF\xBB\xBF/','', $v);
                $v = preg_replace('/[\x00-\x1F\x7F\xC2\xA0\xA0]+/u','', $v);
                return trim($v);
            };
            $get = function(string $key) use ($getRaw, $sanitize) { return $sanitize($getRaw($key)); };

            $emailRaw = (string) $getRaw('email');
            $email = strtolower((string) $sanitize($emailRaw));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $writeErr($rowNum, $email, 'failed', 'email', $emailRaw, 'Invalid email'); $failed++; continue; }

            $username = $get('username') ?: strtok($email, '@');
            $name = $get('name') ?: $username;
            // No longer read role from CSV; honor default role from options if present
            $role = $defaultRole ? 'admin' : 'normal';
            $status = in_array($get('status'), ['active','pending','disabled','suspended']) ? $get('status') : 'active';
            $language = $get('language') ?: config('app.fallback_locale');
            $currency = $get('preferred_currency') ?: Helper::baseCurrencyCode();
            $password = $get('password');
            // send_invite column is removed from CSV; only respect global checkbox
            $send = $sendInvite;

            $user = User::where('email', $email)->first();

            $data = [
                'username' => $username,
                'name' => $name,
                'role' => $role,
                'status' => $status,
                'language' => $language,
                'preferred_currency' => $currency,
            ];

            if ($dryRun) { $skipped++; $writeErr($rowNum, $email, 'skipped', '*', '', 'Dry-run enabled - no write'); continue; }

            try {
                if ($user) {
                    if ($updateExisting) {
                        if ($password) { $data['password'] = Hash::make($password); }
                        $user->update($data); $updated++;
                    } else { $skipped++; $writeErr($rowNum, $email, 'skipped', 'email', $email, 'User exists and update_existing=false'); }
                } else {
                    if (!$password) { $data['password'] = Hash::make(Helper::strRandom()); }
                    $data['email'] = $email;
                    $data['avatar'] = config('settings.avatar');
                    $data['cover']  = config('settings.cover_default') ?? '';
                    $data['verified_id'] = 'yes';
                    // If default role is provided, set legacy admin columns for compatibility
                    if ($defaultRole) {
                        $data['role'] = 'admin';
                        $data['permissions'] = $defaultRole === 'super_admin' ? 'full_access' : 'limited_access';
                    }
                    $user = User::create($data); $created++;
                    // Assign enhanced role if provided
                    if ($defaultRole) {
                        try {
                            UserRole::where('user_id', $user->id)->update(['is_active' => false]);
                            UserRole::create([
                                'user_id' => $user->id,
                                'role_name' => $defaultRole,
                                'permissions' => [],
                                'created_by' => $this->import->admin_id,
                                'is_active' => true,
                            ]);
                        } catch (\Throwable $e) {
                            // continue even if enhanced role assignment fails
                        }
                    }
                    if ($send && !$password) { Password::sendResetLink(['email' => $email]); }
                }
            } catch (\Throwable $e) {
                $failed++; $writeErr($rowNum, $email, 'failed', '*', '', $e->getMessage());
            }
        }

        // Save reports
        rewind($errors);
        $errorsPath = 'imports/errors_'.$this->import->id.'.csv';
        Storage::put($errorsPath, stream_get_contents($errors));
        fclose($errors);

        $summary = [
            'total' => $total,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'failed' => $failed,
            'options' => [
                'default_role' => $defaultRole,
                'update_existing' => $updateExisting,
                'send_invite' => $sendInvite,
                'dry_run' => $dryRun,
            ],
            'file' => $this->import->filename,
        ];
        $summaryPath = 'imports/summary_'.$this->import->id.'.json';
        Storage::put($summaryPath, json_encode($summary));

        $this->import->update([
            'total_rows' => $total,
            'created_count' => $created,
            'updated_count' => $updated,
            'skipped_count' => $skipped,
            'failed_count' => $failed,
            'errors_csv_path' => $errorsPath,
            'summary_json_path' => $summaryPath,
            'status' => 'completed',
            'finished_at' => now(),
        ]);
    }
}


