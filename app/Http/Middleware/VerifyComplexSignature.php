<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyComplexSignature
{
    /*public function handle(Request $request, Closure $next)
    {
        $complexId = $request->route('complex');
        $signature = $request->query('sig');

        if (! $complexId || ! $signature) {
            return response()->json([
                'success' => false,
                'message' => 'Missing signature or complex id'
            ], 403);
        }

        $secret = config('services.remote.secret');

        $expectedSignature = hash_hmac(
            'sha256',
            $complexId,
            $secret
        );

        if (! hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 403);
        }

        return $next($request);
    }*/
    
    public function handle($request, Closure $next)
{
    $complexId = $request->route('complex');
    $timestamp = $request->header('X-TIMESTAMP');
    $signature = $request->header('X-SIGNATURE');

    if (! $complexId || ! $timestamp || ! $signature) {
        return response()->json([
            'success' => false,
            'message' => 'Missing signature or complex id'
        ], 403);
    }

    $secret = env('REMOTE_API_SECRET');

    $expected = hash_hmac(
        'sha256',
        $complexId . '|' . $timestamp,
        $secret
    );

    if (! hash_equals($expected, $signature)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid signature'
        ], 403);
    }

    return $next($request);
}

}
