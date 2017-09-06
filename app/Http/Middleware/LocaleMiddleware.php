<?php

namespace App\Http\Middleware;

use Closure;

class LocaleMiddleware {
    /**
     * @var Application
     */
    private $app;
    /**
     * @var Session
     */
    private $session;
    /**
     * LocaleMiddleware constructor.
     *
     * @param Application $app
     * @param Session $session
     */
    public function __construct(
        Application $app,
        Session $session
    ) {
        $this->app = $app;
        $this->session = $session;
    }
    /**
     * @param $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->has('locale')) {
            $this->session->set('locale', $request->get('locale'));
        }
        $locale = $this->session->get('locale', 'es');
        $this->app->setLocale($locale);
        return $next($request);
    }
}
