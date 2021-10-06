<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 8+.
 *
 * This code should be moved back to \Behat\Mink\Tests\Driver\TestCase when dropping support for
 * PHP 7.1 and older.
 *
 * @internal
 */
abstract class SkippingUnsupportedTestCase extends BaseTestCase
{
    protected function onNotSuccessfulTest(\Throwable $e): void
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }
}
