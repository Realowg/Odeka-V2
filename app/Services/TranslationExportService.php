<?php

namespace App\Services;

use App\Models\Translation;

class TranslationExportService
{
    /**
     * Export translations to CSV
     */
    public function exportCsv($filters = [])
    {
        $query = $this->buildQuery($filters);
        $translations = $query->get();

        $output = fopen('php://temp', 'w');
        
        // Write header
        fputcsv($output, ['locale', 'group', 'key', 'value']);

        // Write data
        foreach ($translations as $translation) {
            fputcsv($output, [
                $translation->locale,
                $translation->group,
                $translation->key,
                $translation->value,
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export translations to JSON
     */
    public function exportJson($filters = [])
    {
        $query = $this->buildQuery($filters);
        $translations = $query->get();

        $data = [];

        foreach ($translations as $translation) {
            $locale = $translation->locale;
            $group = $translation->group;
            $key = $translation->key;
            $value = $translation->value;

            // Handle nested keys (e.g., "validation.required")
            if (!isset($data[$locale])) {
                $data[$locale] = [];
            }

            if (!isset($data[$locale][$group])) {
                $data[$locale][$group] = [];
            }

            // Support dot notation for nested arrays
            $this->setNestedValue($data[$locale][$group], $key, $value);
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Export translations to PHP array format
     */
    public function exportPhp($locale, $group)
    {
        $translations = Translation::where('locale', $locale)
            ->where('group', $group)
            ->get();

        $data = [];

        foreach ($translations as $translation) {
            $this->setNestedValue($data, $translation->key, $translation->value);
        }

        $export = "<?php\n\nreturn " . var_export($data, true) . ";\n";

        return $export;
    }

    /**
     * Build query with filters
     */
    protected function buildQuery($filters)
    {
        $query = Translation::query();

        if (!empty($filters['locale'])) {
            if (is_array($filters['locale'])) {
                $query->whereIn('locale', $filters['locale']);
            } else {
                $query->where('locale', $filters['locale']);
            }
        }

        if (!empty($filters['group'])) {
            if (is_array($filters['group'])) {
                $query->whereIn('group', $filters['group']);
            } else {
                $query->where('group', $filters['group']);
            }
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Exclude empty values if configured
        if (!config('translation.export.include_empty', false)) {
            $query->whereNotNull('value')
                  ->where('value', '!=', '');
        }

        return $query->orderBy('locale')
                    ->orderBy('group')
                    ->orderBy('key');
    }

    /**
     * Set nested array value using dot notation
     */
    protected function setNestedValue(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $current[$k] = $value;
            } else {
                if (!isset($current[$k]) || !is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }
        }
    }

    /**
     * Get filename for export
     */
    public function getFilename($format, $filters = [])
    {
        $parts = ['translations'];

        if (!empty($filters['locale'])) {
            $parts[] = is_array($filters['locale']) 
                ? implode('-', $filters['locale']) 
                : $filters['locale'];
        }

        if (!empty($filters['group'])) {
            $parts[] = is_array($filters['group']) 
                ? implode('-', $filters['group']) 
                : $filters['group'];
        }

        $parts[] = date('Y-m-d-His');

        return implode('_', $parts) . '.' . $format;
    }

    /**
     * Export keys for translation workflow (with source values)
     */
    public function exportKeysToCsv($keys)
    {
        $output = fopen('php://temp', 'w');
        
        // Write header
        fputcsv($output, ['locale', 'group', 'key', 'source_value', 'value']);

        // Write data
        foreach ($keys as $key) {
            fputcsv($output, [
                $key['locale'],
                $key['group'],
                $key['key'],
                $key['source_value'],
                $key['value'],
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export keys to JSON for translation workflow
     */
    public function exportKeysToJson($keys)
    {
        return json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
