<?php

declare(strict_types=1);

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Exception;
use Throwable;
use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit\Runner\Version;
use function version_compare;

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
        protected function onNotSuccessfulTest(Throwable $e): void
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
} elseif (version_compare(Version::id(), '5.0.0', '>=')) {
    /**
     * Implementation of the skipping for UnsupportedDriverActionException for PHPUnit 5.
     *
     * @internal
     */
    class SkippingUnsupportedTestCase extends BaseTestCase
    {
        protected function onNotSuccessfulTest($e): void
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
    class SkippingUnsupportedTestCase extends BaseTestCase
    {
        protected function onNotSuccessfulTest(Exception $e): void
        {
            if ($e instanceof UnsupportedDriverActionException) {
                $this->markTestSkipped($e->getMessage());
            }

            parent::onNotSuccessfulTest($e);
        }
    }
}
