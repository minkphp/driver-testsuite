<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

final class JavascriptTest extends TestCase
{
    public function testAriaRoles():void
    {
        $this->getSession()->visit($this->pathTo('/aria_roles.html'));

        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === false');
        $this->getSession()->getPage()->pressButton('Toggle');
        $this->getSession()->wait(5000, '$("#hidden-element").is(":visible") === true');

        $this->getSession()->getPage()->clickLink('Go to Index');
        $this->assertEquals($this->pathTo('/index.html'), $this->getSession()->getCurrentUrl());
    }

    public function testDragDrop():void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $draggable = $webAssert->elementExists('css', '#draggable');
        $droppable = $webAssert->elementExists('css', '#droppable');

        $draggable->dragTo($droppable);
        $this->assertSame('Dropped left!', $webAssert->elementExists('css', 'p', $droppable)->getText());
    }

    // https://github.com/minkphp/MinkSelenium2Driver/pull/359
    public function testDragDropOntoHiddenItself()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $draggable = $webAssert->elementExists('css', '#draggable2');
        $droppable = $webAssert->elementExists('css', '#draggable2');

        $draggable->dragTo($droppable);
        $this->assertSame('Dropped small!', $webAssert->elementExists('css', '#droppable p')->getText());
    }

    // test accentuated char in button
    public function testIssue225():void
    {
        $this->getSession()->visit($this->pathTo('/issue225.html'));
        $this->getSession()->getPage()->pressButton('Créer un compte');
        $this->getSession()->wait(5000, '$("#panel").text() !== ""');

        $this->assertStringContainsString('OH AIH!', $this->getSession()->getPage()->getText());
    }

    public function testAutocomplete()
    {
        $this->getSession()->visit($this->pathTo('/autocomplete.html'));
        $this->getSession()->getPage()->fillField('autocomplete', 'arc');
        $this->getSession()->wait(5000, '$("ul.ui-autocomplete li").is(":visible") === true');

        $this->assertContains('Arctic fox', $this->getSession()->getPage()->getText());
    }
}
