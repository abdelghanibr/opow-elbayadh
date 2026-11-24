<?php

namespace App\Http\Middleware;

use App\Services\ImpersonationService;
use Closure;
use Illuminate\Http\Request;

/**
 * Bandeau fixe « Vous êtes connecté en tant que … » sur toutes les pages HTML
 * pendant une bascule du super admin (session ouverte par ImpersonationService).
 */
class InjectImpersonationBanner
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            if (!ImpersonationService::isImpersonating() || !auth()->check()) {
                return $response;
            }
            if ($response->isRedirection() || $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
                || $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
                return $response;
            }
            if (!str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
                return $response;
            }

            $html = $response->getContent();
            if (!is_string($html) || stripos($html, '</body>') === false) {
                return $response;
            }

            $u    = auth()->user();
            $name = e(($u->name ?: $u->email) . ' — ' . $u->email . ' (' . $u->type . ')');
            $url  = e(route('impersonate.stop'));
            $csrf = e(csrf_token());

            $banner = <<<HTML
<style>body{padding-top:48px !important}</style>
<div id="impersonation-banner" style="position:fixed;top:0;left:0;right:0;z-index:2147483000;background:#b02a37;color:#fff;
 padding:8px 16px;display:flex;gap:12px;align-items:center;justify-content:center;flex-wrap:wrap;font:600 14px/1.4 system-ui,sans-serif;box-shadow:0 2px 6px rgba(0,0,0,.3)">
  <span>أنت متصل الآن بحساب : {$name}</span>
  <form method="POST" action="{$url}" style="margin:0">
    <input type="hidden" name="_token" value="{$csrf}">
    <button type="submit" style="background:#fff;color:#b02a37;border:0;border-radius:6px;padding:4px 12px;font-weight:700;cursor:pointer">
      العودة إلى حسابي
    </button>
  </form>
</div>
HTML;

            $pos = strripos($html, '</body>');
            $response->setContent(substr($html, 0, $pos) . $banner . substr($html, $pos));
        } catch (\Throwable $e) {
            // ne jamais casser la page
        }

        return $response;
    }
}
