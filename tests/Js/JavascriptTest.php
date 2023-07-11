<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

final class JavascriptTest extends TestCase
{
    public function testAriaRoles(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/aria_roles.html'));

        $session->wait(5000, '$("#hidden-element").is(":visible") === false');
        $session->getPage()->pressButton('Toggle');
        $session->wait(5000, '$("#hidden-element").is(":visible") === true');

        $session->getPage()->clickLink('Go to Index');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        $this->assertEquals($this->pathTo('/index.html'), $session->getCurrentUrl());
    }

    public function testDragDrop(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $draggable = $webAssert->elementExists('css', '#draggable');
        $droppable = $webAssert->elementExists('css', '#droppable');

        $draggable->dragTo($droppable);
        $this->assertEquals('Dropped!', $this->getAssertSession()->elementExists('css', 'p', $droppable)->getText());
    }

    // test accentuated char in button
    public function testIssue225(): void
    {
        $this->getSession()->visit($this->pathTo('/issue225.html'));
        $this->getSession()->getPage()->pressButton('Créer un compte');
        $this->getSession()->wait(5000, '$("#panel").text() != ""');

        $this->assertStringContainsString('OH AIH!', $this->getSession()->getPage()->getText());
    }
}
