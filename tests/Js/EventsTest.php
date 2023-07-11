<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Tests\Driver\TestCase;
use Facebook\WebDriver\WebDriverKeys;
use Generator;

final class EventsTest extends TestCase
{
    /**
     * @group mouse-events
     * @throws ElementNotFoundException
     */
    public function testClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->click();
        $this->assertEquals('single clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     * @throws ElementNotFoundException
     */
    public function testDoubleClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get unbound event and dblclick will not be performed
        // especially if session is not fresh
        usleep(1000000); //1e6

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     * @throws ElementNotFoundException
     */
    public function testRightClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->rightClick();
        $this->assertEquals('right clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     * @throws ElementNotFoundException|ExpectationException
     */
    public function testFocus(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $focusBlurDetector = $this->getAssertSession()->elementExists('css', '.elements input#focus-blur-detector');
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        $focusBlurDetector->focus();
        $this->assertEquals('focused', $focusBlurDetector->getValue());

        $focusableAnchor = $this->getAssertSession()->elementExists('css', '.elements a#focusable');
        $this->assertEquals('no action detected', $focusableAnchor->getText());

        $focusableAnchor->focus();
        // checking that we're on same page
        $this->getAssertSession()->addressEquals('/js_test.html');
        $this->assertEquals('focused', $focusableAnchor->getText());

    }

    /**
     * @group   mouse-events
     * @depends testFocus
     * @throws ElementNotFoundException
     */
    public function testBlur(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $focusBlurDetector = $this->getAssertSession()->elementExists('css', '.elements input#focus-blur-detector');
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        // focusing before, because blur won't be triggered if HTMLElement is not focused
        $focusBlurDetector->focus();
        $focusBlurDetector->blur();
        $this->assertEquals('blured', $focusBlurDetector->getValue());
    }

    /**
     * @group mouse-events
     * @throws ElementNotFoundException
     */
    public function testMouseOver(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $mouseOverDetector = $this->getAssertSession()->elementExists('css', '.elements div#mouseover-detector');
        $this->assertEquals('no mouse action detected', $mouseOverDetector->getText());

        $mouseOverDetector->mouseOver();
        $this->assertEquals('mouse overed', $mouseOverDetector->getText());
    }

    /**
     * @dataProvider provideKeyboardEventsModifiers
     * @throws ElementNotFoundException
     */
    public function testKeyboardEvents(string $string, string $expected): void
    {
        $this->getSession()->visit($this->pathTo('/keyboard_test.html'));
        $webAssert = $this->getAssertSession();

        $input = $webAssert->elementExists('css', '#test-target');
        $event = $webAssert->elementExists('css', '#console-log');

        $input->keyDown($string);
        $input->keyUp($string);

        $text = $event->getHtml();

        $this->assertEquals(
            str_replace("\n\n", "\n", $expected),
            $text
        );
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<int, array{0: WebDriverKeys::*, 1: string}, mixed, void>
     */
    public static function provideKeyboardEventsModifiers(): Generator
    {
        yield [
            WebDriverKeys::LEFT_ALT,
            "Key \"Alt\" pressed  [event: keydown]\nKey \"Alt\" released  [event: keyup]\n"
        ];
        yield [
            WebDriverKeys::LEFT_SHIFT,
            "Key \"Shift\" pressed  [event: keydown]\nKey \"Shift\" released  [event: keyup]\n"
        ];
        yield [
            WebDriverKeys::LEFT_CONTROL,
            "Key \"Control\" pressed  [event: keydown]\nKey \"Control\" released  [event: keyup]\n"
        ];
        yield [
            WebDriverKeys::META,
            "Key \"Meta\" pressed  [event: keydown]\nKey \"Meta\" released  [event: keyup]\n"
        ];
    }
}
