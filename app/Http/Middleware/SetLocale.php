<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales');
        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! array_key_exists($locale, $supportedLocales)) {
            $defaultLocale = config('app.locale');
            $locale = is_string($defaultLocale) && array_key_exists($defaultLocale, $supportedLocales)
                ? $defaultLocale
                : 'en';

            foreach (AcceptHeader::fromString($request->header('Accept-Language'))->all() as $language) {
                if ($language->getQuality() <= 0) {
                    continue;
                }

                $code = strtolower(explode('-', $language->getValue())[0]);
                if (array_key_exists($code, $supportedLocales)) {
                    $locale = $code;
                    break;
                }
            }

            $request->session()->put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
