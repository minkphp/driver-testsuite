<?php


namespace Behat\Mink\Tests\Driver;

use PHPUnit\Framework\TestCase;

// A trait to provide forward compatibility with newest PHPUnit versions
$r = new \ReflectionClass(TestCase::class);
if (\PHP_VERSION_ID < 70000 || !$r->getMethod('onNotSuccessfulTest')->hasReturnType()) {
    $r = $r->getMethod('onNotSuccessfulTest')->getParameters()[0];
    if ((string) $r->getType() === 'Throwable') {
        trait OnNotSuccessfulTrait
        {
            use OnNotSuccessfulTraitForV5;
        }
    } else {
        trait OnNotSuccessfulTrait
        {
            use OnNotSuccessfulTraitForV4;
        }
    }
} else {
    trait OnNotSuccessfulTrait
    {
        use OnNotSuccessfulTraitForV8;
    }
}
