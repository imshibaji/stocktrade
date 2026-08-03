<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class FaqPageTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $namespace = 'App';

    public function testFaqPageIsAccessible(): void
    {
        $result = $this->get('/faq');
        $result->assertStatus(200);
        $result->assertSee('Frequently Asked Questions');
    }

    public function testFaqCoversKeyTopics(): void
    {
        $result = $this->get('/faq');
        $result->assertStatus(200);
        $result->assertSee('real-time');
        $result->assertSee('predictions');
        $result->assertSee('tax');
    }
}
