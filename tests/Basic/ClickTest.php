<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

class ClickTest extends TestCase
{
    public function testClickOutsideViewport()
    {
        $this->getSession()->visit($this->pathTo('/clickoutsideviewport.html'));
        $session->resizeWindow(400, 300);

        $element = $this->getAssertSession()->elementExists('css', '#clickme');
        $element->click();
        $this->assertEquals($this->pathTo('/links.html'), $this->getSession()->getCurrentUrl());
    }
}
