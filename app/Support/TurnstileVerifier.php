<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Server-side check of a Cloudflare Turnstile token (cf-turnstile-response).
 *
 * One widget serves every careers domain, so a token is only accepted when
 * Cloudflare says it is valid, it was made for this form (action) and on the
 * very domain the form is posted to (hostname). Tokens are single use:
 * Cloudflare rejects a replay (timeout-or-duplicate).
 */
class TurnstileVerifier
{
    public const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public const ACTION_APPLY = 'apply';

    private const MAX_TOKEN_LENGTH = 2048;

    public function verify(Request $request, string $expectedAction): bool
    {
        $secretKey = config('services.turnstile.secret_key');
        $token = $request->input('cf-turnstile-response');

        if (! $secretKey || ! is_string($token) || $token === '' || strlen($token) > self::MAX_TOKEN_LENGTH) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post(self::VERIFY_URL, [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        if (! $response->successful() || $response->json('success') !== true) {
            Log::info('Turnstile rejected a token', ['errors' => $response->json('error-codes')]);

            return false;
        }

        $hostname = Str::lower((string) $response->json('hostname'));

        if ($response->json('action') !== $expectedAction || $hostname !== Str::lower($request->getHost())) {
            Log::warning('Turnstile token made for another form or domain', [
                'action' => $response->json('action'),
                'hostname' => $hostname,
                'request_host' => $request->getHost(),
            ]);

            return false;
        }

        return true;
    }
}
