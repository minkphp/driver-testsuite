<?php

namespace Behat\Mink\Tests\Driver\Css;

use Behat\Mink\Tests\Driver\TestCase;

final class VisibilityTest extends TestCase
{
    public function testVisibility(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $clicker = $webAssert->elementExists('css', '.elements div#clicker');
        $invisible = $webAssert->elementExists('css', '#invisible');

        $this->assertFalse($invisible->isVisible());
        $this->assertTrue($clicker->isVisible());
    }
}
