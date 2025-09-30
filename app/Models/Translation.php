<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when translations are modified
        static::saved(function ($translation) {
            // Clear ALL cache keys related to this translation
            Cache::forget("translations.{$translation->locale}.{$translation->group}");
            Cache::forget("trans.{$translation->locale}.{$translation->group}");
            
            // Also clear for all locales of this group (in case it's a shared key)
            $locales = static::getAvailableLocales();
            foreach ($locales as $locale) {
                Cache::forget("trans.{$locale}.{$translation->group}");
                Cache::forget("translations.{$locale}.{$translation->group}");
            }
        });

        static::deleted(function ($translation) {
            // Clear ALL cache keys related to this translation
            Cache::forget("translations.{$translation->locale}.{$translation->group}");
            Cache::forget("trans.{$translation->locale}.{$translation->group}");
            
            // Also clear for all locales
            $locales = static::getAvailableLocales();
            foreach ($locales as $locale) {
                Cache::forget("trans.{$locale}.{$translation->group}");
                Cache::forget("translations.{$locale}.{$translation->group}");
            }
        });
    }

    /**
     * Scope: Filter by locale
     */
    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope: Filter by group
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope: Search by key or value
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('key', 'LIKE', "%{$search}%")
              ->orWhere('value', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get all translations for a specific locale and group
     */
    public static function getGroup($locale, $group)
    {
        return Cache::remember(
            "translations.{$locale}.{$group}",
            3600,
            function () use ($locale, $group) {
                return static::where('locale', $locale)
                    ->where('group', $group)
                    ->pluck('value', 'key')
                    ->toArray();
            }
        );
    }

    /**
     * Set a translation value
     */
    public static function setTranslation($locale, $group, $key, $value)
    {
        return static::updateOrCreate(
            [
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ],
            ['value' => $value]
        );
    }

    /**
     * Get all available locales from Languages table
     */
    public static function getAvailableLocales()
    {
        // Get from admin Languages table
        $languages = \App\Models\Languages::pluck('abbreviation')->toArray();
        
        // If no languages defined, fallback to distinct locales in translations
        if (empty($languages)) {
            $languages = static::distinct('locale')->pluck('locale')->toArray();
        }
        
        return $languages;
    }

    /**
     * Get all available groups
     */
    public static function getAvailableGroups()
    {
        return static::distinct('group')
            ->orderBy('group')
            ->pluck('group')
            ->toArray();
    }
}