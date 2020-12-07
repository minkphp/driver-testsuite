<?php


namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;

trait OnNotSuccessfulTrait
{
    protected function onNotSuccessfulTest(\Throwable $e): void
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }
}
