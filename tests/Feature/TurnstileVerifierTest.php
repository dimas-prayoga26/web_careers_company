<?php

namespace Tests\Feature;

use App\Support\TurnstileVerifier;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileVerifierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.turnstile.secret_key' => 'test-secret']);
    }

    private function application(string $host = 'careers.coffeeniskala.com', mixed $token = 'token-from-the-widget'): Request
    {
        return Request::create("https://{$host}/apply", 'POST', ['cf-turnstile-response' => $token], server: [
            'REMOTE_ADDR' => '203.0.113.7',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function cloudflareSays(array $overrides = [], int $status = 200): void
    {
        Http::fake([TurnstileVerifier::VERIFY_URL => Http::response([
            'success' => true,
            'hostname' => 'careers.coffeeniskala.com',
            'action' => 'apply',
            'error-codes' => [],
            ...$overrides,
        ], $status)]);
    }

    public function test_accepts_a_valid_token_made_by_the_apply_form_on_this_domain(): void
    {
        $this->cloudflareSays();

        $this->assertTrue((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));

        Http::assertSent(fn (ClientRequest $request) => $request->url() === TurnstileVerifier::VERIFY_URL
            && $request->isForm()
            && $request['secret'] === 'test-secret'
            && $request['response'] === 'token-from-the-widget'
            && $request['remoteip'] === '203.0.113.7');
    }

    public function test_rejects_a_token_cloudflare_says_is_invalid_or_already_used(): void
    {
        $this->cloudflareSays(['success' => false, 'error-codes' => ['timeout-or-duplicate']]);

        $this->assertFalse((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));
    }

    public function test_rejects_a_token_solved_on_another_careers_domain(): void
    {
        // The same widget serves every brand; a token from careers.trah.co.id is not for this form.
        $this->cloudflareSays(['hostname' => 'careers.trah.co.id']);

        $this->assertFalse((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));
    }

    public function test_rejects_a_token_made_for_another_action(): void
    {
        $this->cloudflareSays(['action' => 'login']);

        $this->assertFalse((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));
    }

    public function test_compares_the_domain_without_regard_to_case(): void
    {
        $this->cloudflareSays(['hostname' => 'Careers.CoffeeNiskala.com']);

        $this->assertTrue((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));
    }

    public function test_rejects_when_cloudflare_answers_with_an_error_status(): void
    {
        $this->cloudflareSays(status: 500);

        $this->assertFalse((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));
    }

    public function test_rejects_when_cloudflare_cannot_be_reached(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->assertFalse((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));
    }

    public function test_rejects_without_asking_cloudflare_when_the_token_is_missing_or_too_long(): void
    {
        Http::fake();
        $verifier = new TurnstileVerifier;

        $this->assertFalse($verifier->verify($this->application(token: ''), TurnstileVerifier::ACTION_APPLY));
        $this->assertFalse($verifier->verify($this->application(token: ['not', 'a', 'string']), TurnstileVerifier::ACTION_APPLY));
        $this->assertFalse($verifier->verify($this->application(token: str_repeat('a', 2049)), TurnstileVerifier::ACTION_APPLY));

        Http::assertNothingSent();
    }

    public function test_rejects_without_asking_cloudflare_when_no_secret_key_is_set(): void
    {
        config(['services.turnstile.secret_key' => null]);
        Http::fake();

        $this->assertFalse((new TurnstileVerifier)->verify($this->application(), TurnstileVerifier::ACTION_APPLY));

        Http::assertNothingSent();
    }
}
