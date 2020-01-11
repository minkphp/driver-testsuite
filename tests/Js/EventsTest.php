<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class EventsTest extends TestCase
{
    /**
     * @group mouse-events
     */
    public function testClick()
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
    public function testDoubleClick()
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
    public function testRightClick()
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
    public function testFocus()
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
    public function testBlur()
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
    public function testMouseOver()
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $mouseOverDetector = $this->getAssertSession()->elementExists('css', '.elements div#mouseover-detector');
        $this->assertEquals('no mouse action detected', $mouseOverDetector->getText());

        $mouseOverDetector->mouseOver();
        $this->assertEquals('mouse overed', $mouseOverDetector->getText());
    }

    /**
     * @dataProvider provideKeyboardEventsModifiers
     */
    public function testKeyboardEvents($method, $char, $charCode, $modifier, $eventProperties)
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $input = $webAssert->elementExists('css', '.elements input.input');
        $event = $webAssert->elementExists('css', '.elements .text-event');

        $input->$method($char, $modifier);
        $this->assertStringContainsString('event=' . strtolower($method) . ';keyCode=' . $charCode . ';modifier='.$eventProperties, $event->getText());
    }

    public function provideKeyboardEventsModifiers()
    {
        $data = array(
            'none-keyDown' => array('keyDown', 'u', 85, null, '0 / 0 / 0 / 0'),
            'none-keyPress' => array('keyPress', 'b', 98, null, '0 / 0 / 0 / 0'),
            /**
             * @see http://api.jquery.com/keypress/
             *
             * Note that keydown and keyup provide a code indicating which key is pressed,
             * while keypress indicates which character was entered.
             * For example, a lowercase "a" will be reported as 65 by keydown and keyup, but as 97 by keypress.
             * An uppercase "A" is reported as 65 by all events.
             */
            'none-keyUp' => array('keyUp', 110, 78, null, '0 / 0 / 0 / 0'), // 110 = n  78 = N

            // see explanation from above why sending 110 but expecting 78
            'alt-keyUp' => array('keyUp', 110, 78, 'alt', '1 / 0 / 0 / 0'),

            // jQuery considers ctrl as being a metaKey in the normalized event
            'ctrl-keyDown' => array('keyDown', 'u', 85, 'ctrl', '0 / 1 / 0 / 1'),
            // see explanation from above why sending 110 but expecting 78
            'ctrl-keyUp' => array('keyUp', 110, 78, 'ctrl', '0 / 1 / 0 / 1'),

            'shift-keyDown' => array('keyDown', 'u', 85, 'shift', '0 / 0 / 1 / 0'), // 85 = U
            'shift-keyPress' => array('keyPress', 'b', 66, 'shift', '0 / 0 / 1 / 0'), // 66 = B
            // see explanation from above why sending 110 but expecting 78
            'shift-keyUp' => array('keyUp', 110, 78, 'shift', '0 / 0 / 1 / 0'),

            'meta-keyDown' => array('keyDown', 'u', 85, 'meta', '0 / 0 / 0 / 1'),
            'meta-keyPress' => array('keyPress', 'b', 98, 'meta', '0 / 0 / 0 / 1'),
            // see explanation from above why sending 110 but expecting 78
            'meta-keyUp' => array('keyUp', 110, 78, 'meta', '0 / 0 / 0 / 1'),
        );

        // https://bugs.chromium.org/p/chromium/issues/detail?id=13891#c16
        // Control + <char> will not trigger keypress
        // Option + <char> will output different results "special char" ©
        if (PHP_OS !== 'Darwin') {
            $data['alt-keyDown'] = array('keyDown', 'u', 85, 'alt', '1 / 0 / 0 / 0');
            $data['alt-keyPress'] = array('keyPress', 'b', 98, 'alt', '1 / 0 / 0 / 0');
            $data['ctrl-keyPress'] = array('keyPress', 'b', 98, 'ctrl', '0 / 1 / 0 / 1'); // do not use "r" because it will trigger page reload in firefox
        }

        return $data;
    }
}
