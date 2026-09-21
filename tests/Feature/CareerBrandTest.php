<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Support\CareerBrand;
use Illuminate\Http\Request;
use Tests\TestCase;

class CareerBrandTest extends TestCase
{
    public function test_domain_mapping_overrides_previous_session_brand(): void
    {
        session(['career_brand' => 'rnb']);
        $request = Request::create('https://careers.coffeeniskala.com');

        $brand = CareerBrand::resolve($request);

        $this->assertSame('niskala', $brand['key']);
        $this->assertSame('hr@coffeeniskala.com', $brand['email']);
        $this->assertSame('niskala', session('career_brand'));
    }

    public function test_success_page_uses_brand_from_accessed_domain(): void
    {
        $response = $this->get('https://careers.coffeeniskala.com/success');

        $response->assertOk();
        $response->assertSee('Niskala');
        $response->assertSee('Go to Niskala');
        $response->assertSee('https://coffeeniskala.com/', false);
        $response->assertSee('images/Logo Niskala.png', false);
    }

    public function test_success_page_links_rnb_careers_domain_to_rnb_main_website(): void
    {
        $response = $this->get('https://careers.rnb.co.id/success');

        $response->assertOk();
        $response->assertSee('RNB Management');
        $response->assertSee('Go to RNB Management');
        $response->assertSee('https://rnb.co.id/', false);
    }

    public function test_applicant_accepts_resolved_brand_key_for_storage(): void
    {
        $applicant = new Applicant([
            'brand_key' => 'tms',
        ]);

        $this->assertSame('tms', $applicant->brand_key);
    }

    public function test_brand_falls_back_to_default_when_requested_brand_config_is_missing(): void
    {
        config([
            'careers.brands.unknown' => null,
        ]);

        $brand = CareerBrand::brand('unknown');

        $this->assertSame('rnb', $brand['key']);
        $this->assertSame('RNB Management', $brand['name']);
    }
}
