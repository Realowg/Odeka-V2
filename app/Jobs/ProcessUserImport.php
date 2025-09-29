<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserImport;
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

        $stream = Storage::readStream($path);
        if (!$stream) {
            $this->import->update(['status' => 'failed', 'finished_at' => now()]);
            return;
        }

        $errors = fopen('php://temp', 'w+');
        fputcsv($errors, ['row','column','message']);

        $h = fgetcsv($stream); // header
        $map = [];
        foreach ($h as $i => $col) { $map[strtolower(trim($col))] = $i; }

        $rowNum = 1; // header row
        $created = $updated = $skipped = $failed = 0; $total = 0;

        while (($row = fgetcsv($stream)) !== false) {
            $rowNum++; $total++;
            $get = function(string $key) use ($map, $row) { $i = $map[$key] ?? null; return $i !== null ? trim((string)($row[$i] ?? '')) : null; };

            $email = strtolower((string) $get('email'));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { fputcsv($errors, [$rowNum,'email','Invalid email']); $failed++; continue; }

            $username = $get('username') ?: strtok($email, '@');
            $name = $get('name') ?: $username;
            $role = $get('role') ?: ($options['default_role'] ?? 'member');
            $status = in_array($get('status'), ['active','pending','suspended']) ? $get('status') : 'active';
            $language = $get('language') ?: config('app.fallback_locale');
            $currency = $get('preferred_currency') ?: Helper::baseCurrencyCode();
            $password = $get('password');
            $send = $sendInvite || filter_var($get('send_invite'), FILTER_VALIDATE_BOOLEAN);

            $user = User::where('email', $email)->first();

            $data = [
                'username' => $username,
                'name' => $name,
                'role' => $role,
                'status' => $status,
                'language' => $language,
                'preferred_currency' => $currency,
            ];

            if ($dryRun) { $skipped++; continue; }

            try {
                if ($user) {
                    if ($updateExisting) {
                        if ($password) { $data['password'] = Hash::make($password); }
                        $user->update($data); $updated++;
                    } else { $skipped++; }
                } else {
                    if (!$password) { $data['password'] = Hash::make(Helper::strRandom()); }
                    $data['email'] = $email;
                    $data['avatar'] = config('settings.avatar');
                    $data['cover']  = config('settings.cover_default') ?? '';
                    $data['verified_id'] = 'yes';
                    $user = User::create($data); $created++;
                    if ($send && !$password) { Password::sendResetLink(['email' => $email]); }
                }
            } catch (\Throwable $e) {
                $failed++; fputcsv($errors, [$rowNum,'*',$e->getMessage()]);
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


