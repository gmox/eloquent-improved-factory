<?php

namespace Tests\Fixtures;

use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        // if we've made any mockery assertions, add them to PHPUnit's assertion count
        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        Mockery::close();
    }
}
