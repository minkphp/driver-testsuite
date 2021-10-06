<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 6.
 *
 * @internal
 */
abstract class SkippingUnsupportedTestCase extends BaseTestCase
{
    protected function onNotSuccessfulTest(\Throwable $e)
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }
}
