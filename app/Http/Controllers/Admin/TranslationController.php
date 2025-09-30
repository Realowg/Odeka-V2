<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Services\TranslationService;
use App\Services\TranslationImportService;
use App\Services\TranslationExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TranslationController extends Controller
{
    protected $translationService;
    protected $importService;
    protected $exportService;

    public function __construct(
        TranslationService $translationService,
        TranslationImportService $importService,
        TranslationExportService $exportService
    ) {
        $this->translationService = $translationService;
        $this->importService = $importService;
        $this->exportService = $exportService;
    }

    /**
     * Display the translation management page
     */
    public function index(Request $request)
    {
        $filters = [
            'locale' => $request->get('locale'),
            'group' => $request->get('group'),
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 50),
        ];

        $translations = $this->translationService->getTranslations($filters);
        $locales = Translation::getAvailableLocales();
        $groups = Translation::getAvailableGroups();
        $stats = $this->translationService->getStatistics();

        return view('admin.translations.index', compact('translations', 'locales', 'groups', 'filters', 'stats'));
    }

    /**
     * Store or update a translation
     */
    public function store(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|max:10',
            'group' => 'required|string|max:100',
            'key' => 'required|string|max:255',
            'value' => 'nullable|string',
        ]);

        $this->translationService->setTranslation(
            $request->locale,
            $request->group,
            $request->key,
            $request->value
        );

        return back()->with('success_message', __('admin.success_update'));
    }

    /**
     * Update a translation via AJAX
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'nullable|string',
        ]);

        $translation = Translation::findOrFail($id);
        $translation->value = $request->value;
        $translation->save();

        // Force clear ALL related caches
        \Cache::forget("trans.{$translation->locale}.{$translation->group}");
        \Cache::forget("translations.{$translation->locale}.{$translation->group}");

        return response()->json([
            'success' => true,
            'message' => 'Translation updated successfully',
        ]);
    }

    /**
     * Delete a translation
     */
    public function destroy($id)
    {
        $this->translationService->deleteTranslation($id);

        return back()->with('success_message', __('admin.success_delete'));
    }

    /**
     * Bulk delete translations
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:translations,id',
        ]);

        $this->translationService->bulkDelete($request->ids);

        return back()->with('success_message', 'Translations deleted successfully');
    }

    /**
     * Show import page
     */
    public function showImport()
    {
        return view('admin.translations.import');
    }

    /**
     * Handle import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,json,txt|max:10240',
            'mode' => 'required|in:merge,overwrite,add_only',
            'dry_run' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->store('temp/imports');
        $fullPath = Storage::path($path);

        try {
            $options = [
                'mode' => $request->mode,
                'dry_run' => $request->boolean('dry_run'),
            ];

            if ($extension === 'csv' || $extension === 'txt') {
                $result = $this->importService->importCsv($fullPath, $options);
            } elseif ($extension === 'json') {
                $result = $this->importService->importJson($fullPath, $options);
            } else {
                throw new \Exception('Unsupported file format');
            }

            Storage::delete($path);

            $message = sprintf(
                'Import completed: %d created, %d updated, %d skipped',
                $result['created'],
                $result['updated'],
                $result['skipped']
            );

            if (!empty($result['errors'])) {
                $message .= '. ' . count($result['errors']) . ' errors occurred.';
            }

            return back()->with([
                'success_message' => $message,
                'import_result' => $result,
            ]);

        } catch (\Exception $e) {
            Storage::delete($path);
            return back()->with('error_message', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export translations
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,json,php',
            'locale' => 'nullable|array',
            'group' => 'nullable|array',
        ]);

        $filters = [
            'locale' => $request->locale,
            'group' => $request->group,
        ];

        $format = $request->format;
        $filename = $this->exportService->getFilename($format, $filters);

        try {
            if ($format === 'csv') {
                $content = $this->exportService->exportCsv($filters);
                $mimeType = 'text/csv';
            } elseif ($format === 'json') {
                $content = $this->exportService->exportJson($filters);
                $mimeType = 'application/json';
            } elseif ($format === 'php') {
                // PHP export requires single locale and group
                if (empty($filters['locale']) || empty($filters['group'])) {
                    return back()->with('error_message', 'PHP export requires a single locale and group');
                }
                $locale = is_array($filters['locale']) ? $filters['locale'][0] : $filters['locale'];
                $group = is_array($filters['group']) ? $filters['group'][0] : $filters['group'];
                $content = $this->exportService->exportPhp($locale, $group);
                $mimeType = 'text/plain';
            }

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return back()->with('error_message', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Download sample CSV
     */
    public function sampleCsv()
    {
        $content = TranslationImportService::getSampleCsv();

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="translations_sample.csv"');
    }

    /**
     * Download sample JSON
     */
    public function sampleJson()
    {
        $content = TranslationImportService::getSampleJson();

        return response($content)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="translations_sample.json"');
    }

    /**
     * Sync translations from files to database
     */
    public function sync(Request $request)
    {
        $locale = $request->get('locale');
        $group = $request->get('group');

        try {
            $result = $this->translationService->syncFromFiles($locale, $group);

            $message = sprintf(
                'Sync completed: %d synced, %d skipped',
                $result['synced'],
                $result['skipped']
            );

            if (!empty($result['errors'])) {
                $message .= '. ' . count($result['errors']) . ' errors occurred.';
            }

            return back()->with([
                'success_message' => $message,
                'sync_result' => $result,
            ]);

        } catch (\Exception $e) {
            return back()->with('error_message', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Scan Blade files for translation keys
     */
    public function scanKeys()
    {
        try {
            $keys = $this->translationService->scanBladeFilesForKeys();
            
            // Check which keys are missing in database
            $missingKeys = [];
            $locales = Translation::getAvailableLocales();
            
            foreach ($keys as $key) {
                $fullKey = $key['group'] . '.' . $key['key'];
                foreach ($locales as $locale) {
                    $exists = Translation::where('locale', $locale)
                        ->where('group', $key['group'])
                        ->where('key', $key['key'])
                        ->exists();
                    
                    if (!$exists) {
                        $missingKeys[] = [
                            'locale' => $locale,
                            'group' => $key['group'],
                            'key' => $key['key'],
                            'full_key' => $fullKey,
                        ];
                    }
                }
            }
            
            return view('admin.translations.scan-keys', compact('keys', 'missingKeys', 'locales'));
        } catch (\Exception $e) {
            return back()->with('error_message', 'Scan failed: ' . $e->getMessage());
        }
    }

    /**
     * Import missing keys from scan to database
     */
    public function importMissingKeys(Request $request)
    {
        try {
            $keys = $request->input('keys', []);
            $imported = 0;
            
            foreach ($keys as $keyData) {
                if (!isset($keyData['locale'], $keyData['group'], $keyData['key'])) {
                    continue;
                }
                
                // Create translation with empty value
                Translation::firstOrCreate(
                    [
                        'locale' => $keyData['locale'],
                        'group' => $keyData['group'],
                        'key' => $keyData['key'],
                    ],
                    ['value' => ''] // Empty value, to be filled later
                );
                
                $imported++;
            }
            
            return back()->with('success_message', "Successfully imported {$imported} missing translation keys.");
        } catch (\Exception $e) {
            return back()->with('error_message', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Find unused translation keys
     */
    public function findUnused()
    {
        try {
            $unusedKeys = $this->translationService->findUnusedKeys();
            return view('admin.translations.unused-keys', compact('unusedKeys'));
        } catch (\Exception $e) {
            return back()->with('error_message', 'Failed to find unused keys: ' . $e->getMessage());
        }
    }

    /**
     * Export keys for translation (source locale with empty target)
     */
    public function exportForTranslation(Request $request)
    {
        $request->validate([
            'source_locale' => 'required|string|max:10',
            'target_locale' => 'required|string|max:10',
            'format' => 'required|in:csv,json',
        ]);

        $sourceLocale = $request->source_locale;
        $targetLocale = $request->target_locale;
        $format = $request->format;

        try {
            $keys = $this->translationService->exportKeysForTranslation($sourceLocale, $targetLocale);

            if ($format === 'csv') {
                $content = $this->exportService->exportKeysToCsv($keys);
                $mimeType = 'text/csv';
                $filename = "translations_{$sourceLocale}_to_{$targetLocale}.csv";
            } else {
                $content = $this->exportService->exportKeysToJson($keys);
                $mimeType = 'application/json';
                $filename = "translations_{$sourceLocale}_to_{$targetLocale}.json";
            }

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return back()->with('error_message', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear all translation caches
     */
    public function clearCache()
    {
        try {
            // Get all locales and groups
            $locales = Translation::getAvailableLocales();
            $groups = Translation::getAvailableGroups();
            
            $cleared = 0;
            foreach ($locales as $locale) {
                foreach ($groups as $group) {
                    \Cache::forget("trans.{$locale}.{$group}");
                    \Cache::forget("translations.{$locale}.{$group}");
                    $cleared++;
                }
            }

            return back()->with('success_message', "Successfully cleared {$cleared} translation cache entries. All translations will be reloaded from database.");
        } catch (\Exception $e) {
            return back()->with('error_message', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}