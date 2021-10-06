<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 4.
 *
 * @internal
 */
abstract class SkippingUnsupportedTestCase extends \PHPUnit_Framework_TestCase
{
    protected function onNotSuccessfulTest(\Exception $e)
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }
}
