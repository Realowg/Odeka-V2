<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class TranslationService
{
    /**
     * Get translations with filters
     */
    public function getTranslations(array $filters = [])
    {
        $query = Translation::query();

        // Filter by locale
        if (!empty($filters['locale'])) {
            $query->where('locale', $filters['locale']);
        }

        // Filter by group
        if (!empty($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        // Search in key or value
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Order by locale, group, key
        $query->orderBy('locale')->orderBy('group')->orderBy('key');

        // Paginate with custom per_page
        $perPage = $filters['per_page'] ?? 50;
        return $query->paginate($perPage)->appends($filters);
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_translations' => Translation::count(),
            'locales' => Translation::getAvailableLocales(),
            'groups' => Translation::getAvailableGroups(),
            'by_locale' => [],
        ];

        // Count by locale
        foreach ($stats['locales'] as $locale) {
            $stats['by_locale'][$locale] = Translation::where('locale', $locale)->count();
        }

        return $stats;
    }

    /**
     * Set a translation value
     */
    public function setTranslation($locale, $group, $key, $value)
    {
        return Translation::setTranslation($locale, $group, $key, $value);
    }

    /**
     * Delete a translation by ID
     */
    public function deleteTranslation($id)
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();
        return true;
    }

    /**
     * Bulk delete translations
     */
    public function bulkDelete(array $ids)
    {
        Translation::whereIn('id', $ids)->delete();
        return true;
    }

    /**
     * Sync translations from PHP files to database
     */
    public function syncFromFiles($locale = null, $group = null)
    {
        $result = ['synced' => 0, 'skipped' => 0, 'errors' => []];
        $langPath = base_path('lang');

        if (!File::isDirectory($langPath)) {
            $result['errors'][] = "Language directory not found: {$langPath}";
            return $result;
        }

        // Get all language directories (en, fr, es, etc.)
        $locales = $locale ? [$locale] : array_filter(
            File::directories($langPath),
            fn($dir) => !str_contains($dir, 'vendor')
        );

        foreach ($locales as $localeDir) {
            $localeCode = is_string($localeDir) ? basename($localeDir) : $locale;

            // Get all PHP files in this locale directory
            $files = File::glob("{$langPath}/{$localeCode}/*.php");

            foreach ($files as $file) {
                $groupName = pathinfo($file, PATHINFO_FILENAME);

                // Skip if filtering by group
                if ($group && $groupName !== $group) {
                    continue;
                }

                try {
                    $translations = include $file;

                    if (!is_array($translations)) {
                        $result['errors'][] = "File {$file} does not return an array";
                        continue;
                    }

                    // Flatten nested arrays (e.g., 'user.name' => 'Name')
                    $flatTranslations = Arr::dot($translations);

                    foreach ($flatTranslations as $key => $value) {
                        // Skip if not a string value
                        if (!is_string($value)) {
                            continue;
                        }

                        // Check if translation already exists
                        $existing = Translation::where('locale', $localeCode)
                            ->where('group', $groupName)
                            ->where('key', $key)
                            ->first();

                        if (!$existing) {
                            Translation::create([
                                'locale' => $localeCode,
                                'group' => $groupName,
                                'key' => $key,
                                'value' => $value,
                            ]);
                            $result['synced']++;
                        } else {
                            $result['skipped']++;
                        }
                    }
                } catch (\Exception $e) {
                    $result['errors'][] = "Error processing {$file}: " . $e->getMessage();
                }
            }
        }

        return $result;
    }

    /**
     * Scan all Blade files and get all translation keys
     */
    public function scanBladeFilesForKeys()
    {
        $keys = [];
        $viewsPath = resource_path('views');

        if (!File::isDirectory($viewsPath)) {
            return $keys;
        }

        // Get all blade files
        $files = File::allFiles($viewsPath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getRealPath());

            // Match patterns like __('key'), trans('key'), @lang('key')
            preg_match_all('/(?:__|trans|@lang)\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    // Parse group.key format
                    if (str_contains($match, '.')) {
                        [$group, $key] = explode('.', $match, 2);
                        $keys[] = ['group' => $group, 'key' => $key, 'file' => $file->getRelativePath() . '/' . $file->getFilename()];
                    }
                }
            }
        }

        return $keys;
    }

    /**
     * Find unused translation keys
     */
    public function findUnusedKeys()
    {
        $usedKeys = [];
        $viewsPath = resource_path('views');
        $controllersPath = app_path('Http/Controllers');

        // Scan Blade files
        $files = array_merge(
            File::allFiles($viewsPath),
            File::allFiles($controllersPath)
        );

        foreach ($files as $file) {
            if (!in_array($file->getExtension(), ['php'])) {
                continue;
            }

            $content = File::get($file->getRealPath());

            // Match patterns like __('group.key'), trans('group.key'), @lang('group.key')
            preg_match_all('/(?:__|trans|@lang)\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $usedKeys[] = $match;
                }
            }
        }

        // Get all keys from database
        $allTranslations = Translation::select('group', 'key')->distinct()->get();

        $unusedKeys = [];
        foreach ($allTranslations as $translation) {
            $fullKey = $translation->group . '.' . $translation->key;
            if (!in_array($fullKey, $usedKeys)) {
                $unusedKeys[] = [
                    'group' => $translation->group,
                    'key' => $translation->key,
                    'full_key' => $fullKey,
                ];
            }
        }

        return $unusedKeys;
    }

    /**
     * Export all keys for a specific locale (for translation workflow)
     */
    public function exportKeysForTranslation($sourceLocale = 'en', $targetLocale = null)
    {
        $keys = [];

        // Get all translations from source locale
        $sourceTranslations = Translation::where('locale', $sourceLocale)
            ->orderBy('group')
            ->orderBy('key')
            ->get();

        foreach ($sourceTranslations as $translation) {
            $keyData = [
                'locale' => $targetLocale ?? '',
                'group' => $translation->group,
                'key' => $translation->key,
                'source_value' => $translation->value,
                'value' => '', // Empty for translation
            ];

            // If target locale specified, try to get existing translation
            if ($targetLocale) {
                $existing = Translation::where('locale', $targetLocale)
                    ->where('group', $translation->group)
                    ->where('key', $translation->key)
                    ->first();

                if ($existing) {
                    $keyData['value'] = $existing->value;
                }
            }

            $keys[] = $keyData;
        }

        return $keys;
    }
}