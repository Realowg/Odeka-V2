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
      // Priority 1: Logged-in user's language preference
      if (auth()->check() && auth()->user()->language != '') {
        $locale = auth()->user()->language;
        app()->setLocale($locale);
        Session::put('locale', $locale);
      }
      // Priority 2: Session locale (from language switcher)
      elseif (Session::has('locale')) {
        $locale = session('locale');
        app()->setLocale($locale);
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
          
          app()->setLocale($detectedLocale);
          Session::put('locale', $detectedLocale);
          
        } catch (\Exception $e) {
          // Fallback to config locale if detection fails
          $fallbackLocale = config('app.fallback_locale', 'en');
          app()->setLocale($fallbackLocale);
          Session::put('locale', $fallbackLocale);
        }
      }

      return $next($request);
    }
}
