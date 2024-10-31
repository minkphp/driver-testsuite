<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

final class WindowTest extends TestCase
{
    public function testWindow(): void
    {
        $this->getSession()->visit($this->pathTo('/window.html'));
        $session = $this->getSession();
        $page = $session->getPage();
        $webAssert = $this->getAssertSession();

        $page->clickLink('Popup #1');
        $session->switchToWindow(null);

        $page->clickLink('Popup #2');
        $session->switchToWindow(null);

        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());

        $session->switchToWindow('popup_1');
        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Popup#1 div text', $el->getText());

        $session->switchToWindow('popup_2');
        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Popup#2 div text', $el->getText());

        $session->switchToWindow(null);
        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());
    }

    public function testWindowName(): void
    {
        $this->getSession()->visit($this->pathTo('/window.html'));
        $windowNames = $this->getSession()->getWindowNames();
        $this->assertArrayHasKey(0, $windowNames);

        $windowName = $this->getSession()->getWindowName();

        $this->assertIsString($windowName);
        $this->assertContains($windowName, $windowNames, 'The current window name should be one of the available window names.');
    }

    public function testGetWindowNames(): void
    {
        $this->getSession()->visit($this->pathTo('/window.html'));
        $session = $this->getSession();
        $page = $session->getPage();

        $windowName = $this->getSession()->getWindowName();

        $this->assertNotNull($windowName);

        $page->clickLink('Popup #1');
        $page->clickLink('Popup #2');

        $windowNames = $this->getSession()->getWindowNames();

        $this->assertNotNull($windowNames[0]);
        $this->assertNotNull($windowNames[1]);
        $this->assertNotNull($windowNames[2]);
    }

    public function testResizeWindow(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $session = $this->getSession();
        $expectedWidth = 640;
        $expectedHeight = 480;

        $session->resizeWindow($expectedWidth, $expectedHeight);
        $session->wait(1000, 'false');

        $jsWindowSizeScript = <<<"JS"
        (function () {
            var check = function (actualWidth, actualHeight) {
                    return Math.abs(actualWidth - $expectedWidth) <= 100
                        && Math.abs(actualHeight - $expectedHeight) <= 100;
                    },
                htmlElem = document.documentElement,
                bodyElem = document.getElementsByTagName('body')[0];

            return check(window.outerWidth, window.outerHeight)
                || check(
                    window.innerWidth || htmlElem.clientWidth || bodyElem.clientWidth,
                    window.innerHeight || htmlElem.clientHeight || bodyElem.clientHeight
                );
        })();
JS;
        $this->assertTrue($session->evaluateScript($jsWindowSizeScript));
    }

    public function testWindowMaximize(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $session = $this->getSession();
        $popupName = 'testPopup';
        $createWindowJs = "window.open('about:blank', '$popupName', 'left=20,top=40,width=300,height=200')";
        $getWindowPosJs = '
            return {
                top: window.screenY,
                left: window.screenX,
                right: window.screenX + window.innerWidth,
                bottom: window.screenX + window.innerHeight
            }
        ';
        $session->executeScript($createWindowJs);
        $session->switchToWindow($popupName);
        $oldDim = (array)$session->evaluateScript($getWindowPosJs);

        $session->maximizeWindow($popupName);
        $newDim = (array)$session->evaluateScript($getWindowPosJs);

        foreach (array_keys($oldDim) as $name) {
            $this->assertNotEquals(
                $oldDim[$name],
                $newDim[$name],
                "The popup's $name position should not be the same after maximizing"
            );
        }
    }
}
