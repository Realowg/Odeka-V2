<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Translation\FileLoader;
use Illuminate\Support\Facades\Cache;

class DatabaseTranslationLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        // If namespace is specified, use file loader (for packages)
        if ($namespace && $namespace !== '*') {
            return parent::load($locale, $group, $namespace);
        }

        // Check if database translations are enabled
        if (config('translation.source') !== 'database') {
            return parent::load($locale, $group, $namespace);
        }

        // Try to load from database first
        $databaseTranslations = $this->loadFromDatabase($locale, $group);

        // If we have database translations, return them
        if (!empty($databaseTranslations)) {
            return $databaseTranslations;
        }

        // Fallback to file-based translations if enabled
        if (config('translation.fallback_to_files', true)) {
            return parent::load($locale, $group, $namespace);
        }

        return [];
    }

    /**
     * Load translations from database
     *
     * @param  string  $locale
     * @param  string  $group
     * @return array
     */
    protected function loadFromDatabase($locale, $group)
    {
        $cacheKey = "trans.{$locale}.{$group}";

        if (config('translation.cache.enabled', true)) {
            return Cache::remember($cacheKey, config('translation.cache.ttl', 3600), function () use ($locale, $group) {
                return $this->fetchFromDatabase($locale, $group);
            });
        }

        return $this->fetchFromDatabase($locale, $group);
    }

    /**
     * Fetch translations from database
     *
     * @param  string  $locale
     * @param  string  $group
     * @return array
     */
    protected function fetchFromDatabase($locale, $group)
    {
        try {
            $translations = Translation::where('locale', $locale)
                ->where('group', $group)
                ->pluck('value', 'key')
                ->toArray();

            // Convert dot notation keys to nested arrays
            return $this->expandDotNotation($translations);
        } catch (\Exception $e) {
            // If database is not available, return empty array
            return [];
        }
    }

    /**
     * Expand dot notation keys to nested arrays
     *
     * @param  array  $translations
     * @return array
     */
    protected function expandDotNotation(array $translations)
    {
        $result = [];

        foreach ($translations as $key => $value) {
            $this->setNestedValue($result, $key, $value);
        }

        return $result;
    }

    /**
     * Set a nested array value using dot notation
     *
     * @param  array  $array
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    protected function setNestedValue(&$array, $key, $value)
    {
        if (!str_contains($key, '.')) {
            $array[$key] = $value;
            return;
        }

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
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        parent::addNamespace($namespace, $hint);
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addJsonPath($path)
    {
        parent::addJsonPath($path);
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        return $this->hints;
    }
}
