<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Models\Languages;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $locale = null;
      
      // Priority 1: Logged-in user's language preference
      if (auth()->check() && auth()->user()->language != '') {
        $locale = auth()->user()->language;
      }
      // Priority 2: Session locale (from language switcher)
      elseif (Session::has('locale')) {
        $locale = session('locale');
      }
      // Priority 3: Browser language detection
      else {
        try {
          $fallbackLocale = config('app.fallback_locale', 'en');
          $detectedLocale = $fallbackLocale;
          
          $availableLangs = Languages::all()->pluck('abbreviation')->toArray();
          $acceptLanguage = $request->server('HTTP_ACCEPT_LANGUAGE');
          
          if ($acceptLanguage && !empty($availableLangs)) {
            $userLangs = explode(',', $acceptLanguage);
            
            foreach ($availableLangs as $lang) {
              if (strpos($userLangs[0], $lang) !== false) {
                $detectedLocale = $lang;
                break;
              }
            }
          }
          
          $locale = $detectedLocale;
          Session::put('locale', $locale);
          
        } catch (\Exception $e) {
          // Fallback to config locale if detection fails
          $locale = config('app.fallback_locale', 'en');
          Session::put('locale', $locale);
        }
      }

      // CRITICAL: Set locale once at the end, ensuring consistency
      if ($locale) {
        app()->setLocale($locale);
        // Also ensure session is set
        if (!Session::has('locale')) {
          Session::put('locale', $locale);
        }
      }

      return $next($request);
    }
}
