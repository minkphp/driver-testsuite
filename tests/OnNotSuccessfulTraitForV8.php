<?php


namespace Behat\Mink\Tests\Driver;


use Behat\Mink\Exception\UnsupportedDriverActionException;

trait OnNotSuccessfulTraitForV8
{
    protected function onNotSuccessfulTest(\Throwable $e): void
    {
        if ($e instanceof UnsupportedDriverActionException) {
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }
}