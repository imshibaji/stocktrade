<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class GuestPagesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $namespace = 'App';

    public function testPricingPageIsAccessible(): void
    {
        $result = $this->get('/pricing');
        $result->assertStatus(200);
        $result->assertSee('Pricing');
        $result->assertSee('Free');
    }

    public function testTermsPageIsAccessible(): void
    {
        $result = $this->get('/terms');
        $result->assertStatus(200);
        $result->assertSee('Terms and Conditions');
        $result->assertSee('Last updated');
    }

    public function testPrivacyPageIsAccessible(): void
    {
        $result = $this->get('/privacy');
        $result->assertStatus(200);
        $result->assertSee('Privacy Policy');
        $result->assertSee('Last updated');
    }

    public function testUserDocumentationPageIsAccessible(): void
    {
        $result = $this->get('/docs/user');
        $result->assertStatus(200);
        $result->assertSee('User Guide');
        $result->assertSee('dashboard');
    }

    public function testDeveloperDocumentationPageIsAccessible(): void
    {
        $result = $this->get('/docs/developer');
        $result->assertStatus(200);
        $result->assertSee('Developer Documentation');
        $result->assertSee('API');
    }
}
