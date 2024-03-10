<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\KeyModifier;
use Behat\Mink\Tests\Driver\TestCase;

final class EventsTest extends TestCase
{
    /**
     * @group mouse-events
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
     */
    public function testDoubleClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
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
     * @group mouse-events
     * @depends testFocus
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
     * @param KeyModifier::*|null $modifier
     *
     * @dataProvider provideKeyboardEventsModifiers
     */
    public function testKeyboardEvents(?string $modifier, string $eventProperties): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $input1 = $webAssert->elementExists('css', '.elements input.input.first');
        $input2 = $webAssert->elementExists('css', '.elements input.input.second');
        $input3 = $webAssert->elementExists('css', '.elements input.input.third');
        $event = $webAssert->elementExists('css', '.elements .text-event');

        $input1->keyDown('u', $modifier);
        $this->assertEquals('key downed:' . $eventProperties, $event->getText());

        $input2->keyPress('r', $modifier);
        $this->assertEquals('key pressed:114 / ' . $eventProperties, $event->getText());

        $input2->keyPress('R', $modifier);
        $this->assertEquals('key pressed:82 / ' . $eventProperties, $event->getText());

        $input2->keyPress('0', $modifier);
        $this->assertEquals('key pressed:48 / ' . $eventProperties, $event->getText());

        $input3->keyUp(78, $modifier);
        $this->assertEquals('key upped:78 / ' . $eventProperties, $event->getText());
    }

    public static function provideKeyboardEventsModifiers(): iterable
    {
        return [
            'none' => [null, '0 / 0 / 0 / 0'],
            'alt' => [KeyModifier::ALT, '1 / 0 / 0 / 0'],
            // jQuery considers ctrl as being a metaKey in the normalized event
            'ctrl' => [KeyModifier::CTRL, '0 / 1 / 0 / 1'],
            'shift' => [KeyModifier::SHIFT, '0 / 0 / 1 / 0'],
            'meta' => [KeyModifier::META, '0 / 0 / 0 / 1'],
        ];
    }
}
