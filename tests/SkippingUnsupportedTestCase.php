<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit\Runner\Version;

if (class_exists('PHPUnit\Runner\Version') && version_compare(Version::id(), '6.0.0', '>=')) {
    /**
     * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 6+.
     *
     * This code should be moved back to \Behat\Mink\Tests\Driver\TestCase when dropping support for
     * PHP 5.6 and older, as PHPUnit 4 and 5 won't be needed anymore.
     *
     * @internal
     */
    class SkippingUnsupportedTestCase extends BaseTestCase
    {
        protected function onNotSuccessfulTest(\Throwable $e)
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
} elseif (version_compare(\PHPUnit_Runner_Version::id(), '5.0.0', '>=')) {
    /**
     * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 5.
     *
     * @internal
     */
    class SkippingUnsupportedTestCase extends \PHPUnit_Framework_TestCase
    {
        protected function onNotSuccessfulTest($e)
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
} else {
    /**
     * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 4.
     *
     * @internal
     */
    class SkippingUnsupportedTestCase extends \PHPUnit_Framework_TestCase
    {
        protected function onNotSuccessfulTest(\Exception $e)
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
}
