<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class WindowTest extends TestCase
{
    public function testWindow()
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

    public function testGetWindowNames()
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

    public function testResizeWindow()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $session = $this->getSession();

        $width = 500;
        $height = 300;
        $session->resizeWindow($width, $height);
        $session->wait(1000, 'false');
        $jsWindowSizeScript = <<<JS
        (function(){
          var boolSizeCheck = Math.abs(window.outerHeight - $height) <= 100 && Math.abs(window.outerWidth - $width) <= 100;
          if (boolSizeCheck){
            return true;
          }
          var w = window,
              d = document,
              e = d.documentElement,
              g = d.getElementsByTagName('body')[0],
              x = w.innerWidth || e.clientWidth || g.clientWidth,
              y = w.innerHeight|| e.clientHeight|| g.clientHeight;
          boolSizeCheck = Math.abs(y - $height) <= 100 && Math.abs(x - $width) <= 100;
          return boolSizeCheck;
        })();
JS;

        $result = $session->evaluateScript($jsWindowSizeScript);
        $this->assertTrue($result);
    }

    public function testWindowMaximize()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $session = $this->getSession();

        $session->maximizeWindow();
        $session->wait(1000, 'false');

        $script = 'return Math.abs(screen.availHeight - window.outerHeight) <= 100;';

        $this->assertTrue($session->evaluateScript($script));
    }
}
