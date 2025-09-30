<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TranslationImportService
{
    protected $stats = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    /**
     * Import translations from CSV
     */
    public function importCsv($filePath, $options = [])
    {
        $mode = $options['mode'] ?? 'merge'; // 'merge', 'overwrite', 'add_only'
        $dryRun = $options['dry_run'] ?? false;

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Unable to open CSV file');
        }

        // Read header
        $header = fgetcsv($handle);
        if (!$this->validateCsvHeader($header)) {
            fclose($handle);
            throw new \Exception('Invalid CSV header. Expected: locale,group,key,value');
        }

        $rowNumber = 1;
        
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                if (count($row) < 4) {
                    $this->stats['errors'][] = "Row {$rowNumber}: Invalid format";
                    continue;
                }

                [$locale, $group, $key, $value] = $row;

                // Validate
                $validator = Validator::make(
                    compact('locale', 'group', 'key'),
                    [
                        'locale' => 'required|string|max:10',
                        'group' => 'required|string|max:100',
                        'key' => 'required|string|max:255',
                    ]
                );

                if ($validator->fails()) {
                    $this->stats['errors'][] = "Row {$rowNumber}: " . $validator->errors()->first();
                    continue;
                }

                if (!$dryRun) {
                    $this->processRow($locale, $group, $key, $value, $mode);
                }
            }

            if (!$dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }

        fclose($handle);

        return $this->stats;
    }

    /**
     * Import translations from JSON
     */
    public function importJson($filePath, $options = [])
    {
        $mode = $options['mode'] ?? 'merge';
        $dryRun = $options['dry_run'] ?? false;

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
        }

        DB::beginTransaction();

        try {
            $this->processJsonData($data, $mode, $dryRun);

            if (!$dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->stats;
    }

    /**
     * Process JSON data recursively
     */
    protected function processJsonData($data, $mode, $dryRun, $locale = null, $group = null, $prefix = '')
    {
        foreach ($data as $key => $value) {
            // Top level: locale
            if ($locale === null) {
                $this->processJsonData($value, $mode, $dryRun, $key);
                continue;
            }

            // Second level: group
            if ($group === null) {
                $this->processJsonData($value, $mode, $dryRun, $locale, $key);
                continue;
            }

            // Nested arrays
            if (is_array($value)) {
                $newPrefix = $prefix ? "{$prefix}.{$key}" : $key;
                $this->processJsonData($value, $mode, $dryRun, $locale, $group, $newPrefix);
                continue;
            }

            // Leaf node: actual translation
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (!$dryRun) {
                $this->processRow($locale, $group, $fullKey, $value, $mode);
            }
        }
    }

    /**
     * Process a single translation row
     */
    protected function processRow($locale, $group, $key, $value, $mode)
    {
        $existing = Translation::where('locale', $locale)
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        if ($existing) {
            if ($mode === 'add_only') {
                $this->stats['skipped']++;
                return;
            }

            if ($mode === 'merge' || $mode === 'overwrite') {
                $existing->value = $value;
                $existing->save();
                $this->stats['updated']++;
                return;
            }
        }

        // Create new
        Translation::create([
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
            'value' => $value,
        ]);

        $this->stats['created']++;
    }

    /**
     * Validate CSV header
     */
    protected function validateCsvHeader($header)
    {
        $expected = ['locale', 'group', 'key', 'value'];
        return count($header) >= 4 && 
               strtolower(trim($header[0])) === 'locale' &&
               strtolower(trim($header[1])) === 'group' &&
               strtolower(trim($header[2])) === 'key' &&
               strtolower(trim($header[3])) === 'value';
    }

    /**
     * Generate sample CSV content
     */
    public static function getSampleCsv()
    {
        return "locale,group,key,value\n" .
               "en,odeka,hero_headline,\"We create, distribute & monetize content for brands and creators.\"\n" .
               "en,odeka,hero_sub,\"Odeka Media is a content studio and platform.\"\n" .
               "fr,odeka,hero_headline,\"Nous créons, distribuons et monétisons du contenu pour les marques et les créateurs.\"\n" .
               "fr,odeka,hero_sub,\"Odeka Media est un studio de contenu et une plateforme.\"\n";
    }

    /**
     * Generate sample JSON content
     */
    public static function getSampleJson()
    {
        return json_encode([
            'en' => [
                'odeka' => [
                    'hero_headline' => 'We create, distribute & monetize content for brands and creators.',
                    'hero_sub' => 'Odeka Media is a content studio and platform.',
                ],
            ],
            'fr' => [
                'odeka' => [
                    'hero_headline' => 'Nous créons, distribuons et monétisons du contenu pour les marques et les créateurs.',
                    'hero_sub' => 'Odeka Media est un studio de contenu et une plateforme.',
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
