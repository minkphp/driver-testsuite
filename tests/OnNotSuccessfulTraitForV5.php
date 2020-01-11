<?php


namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\UnsupportedDriverActionException;

trait OnNotSuccessfulTraitForV5
{
    protected function onNotSuccessfulTest(\Throwable $e)
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }
}