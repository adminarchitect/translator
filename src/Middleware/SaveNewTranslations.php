<?php

namespace Terranet\Translator\Middleware;

use Closure;
use Terranet\Translator\DbLoader;
use Terranet\Translator\Translator;

class SaveNewTranslations
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
        $response = $next($request);

        /** @var DbLoader $loader */
        $loader = app('translation.loader');
        if ($loader instanceof DbLoader) {
            $loader->save(app('translator')->getNewLines());
        }

        return $response;
    }
}
