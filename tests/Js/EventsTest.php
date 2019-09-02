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
     * @group legacy
     */
    public function testKeyboardEvents($modifier, $eventProperties)
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $input1 = $webAssert->elementExists('css', '.elements input.input.first');
        $input2 = $webAssert->elementExists('css', '.elements input.input.second');
        $input3 = $webAssert->elementExists('css', '.elements input.input.third');
        $event = $webAssert->elementExists('css', '.elements .text-event');

        $input1->keyDown('u', $modifier);
        $this->assertEquals('key downed:'.$eventProperties, $event->getText());

        $input2->keyPress('r', $modifier);
        $this->assertEquals('key pressed:114 / '.$eventProperties, $event->getText());

        $input3->keyUp(78, $modifier);
        $this->assertEquals('key upped:78 / '.$eventProperties, $event->getText());
    }

    public function provideKeyboardEventsModifiers()
    {
        return array(
            'none' => array(null, '0 / 0 / 0 / 0'),
            'alt' => array('alt', '1 / 0 / 0 / 0'),
             // jQuery considers ctrl as being a metaKey in the normalized event
            'ctrl' => array('ctrl', '0 / 1 / 0 / 1'),
            'shift' => array('shift', '0 / 0 / 1 / 0'),
            'meta' => array('meta', '0 / 0 / 0 / 1'),
        );
    }

    /**
     * @dataProvider provideKeyboardEventsPressKeyModifiers
     */
    public function testKeyboardEventsPressKey($modifier, $char, array $expected)
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $webAssert = $this->getAssertSession();

        $input = $webAssert->elementExists('css', '.elements input.input.fourth');
        $event = $webAssert->elementExists('css', '.elements .text-event');

        $input->pressKey($char, $modifier);
        $this->assertEquals($expected, array_map('trim', array_filter(explode(';', $event->getText()))));
    }

    public function provideKeyboardEventsPressKeyModifiers()
    {
        /**
         * @see http://api.jquery.com/keypress/
         *
         * Note that keydown and keyup provide a code indicating which key is pressed,
         * while keypress indicates which character was entered.
         * For example, a lowercase "a" will be reported as 65 by keydown and keyup, but as 97 by keypress.
         * An uppercase "A" is reported as 65 by all events.
         */
        return array(
            'none' => array(null, 'u', array( // u = 117 U = 85
                'event=keydown keyCode=85 modifier=0 / 0 / 0 / 0',
                'event=keypress keyCode=117 modifier=0 / 0 / 0 / 0',
                'event=keyup keyCode=85 modifier=0 / 0 / 0 / 0'
            )),
            'alt' => array('alt', 'a', array( // a = 97 A = 65
                'event=keydown keyCode=18 modifier=1 / 0 / 0 / 0',
                'event=keydown keyCode=65 modifier=1 / 0 / 0 / 0',
                'event=keypress keyCode=97 modifier=1 / 0 / 0 / 0',
                'event=keyup keyCode=65 modifier=1 / 0 / 0 / 0',
                'event=keyup keyCode=18 modifier=0 / 0 / 0 / 0'
            )),
            // do not use ctrl+r it will force browser to reload (firefox)
            // jQuery considers ctrl as being a metaKey in the normalized event
            'ctrl' => array('ctrl', 'b', array( // b = 98 B = 66
                'event=keydown keyCode=17 modifier=0 / 1 / 0 / 1',
                'event=keydown keyCode=66 modifier=0 / 1 / 0 / 1',
                'event=keypress keyCode=98 modifier=0 / 1 / 0 / 1',
                'event=keyup keyCode=66 modifier=0 / 1 / 0 / 1',
                'event=keyup keyCode=17 modifier=0 / 0 / 0 / 0'
            )),
            'shift' => array('shift', 'c', array( // c = 99 C = 67
                'event=keydown keyCode=16 modifier=0 / 0 / 1 / 0',
                'event=keydown keyCode=67 modifier=0 / 0 / 1 / 0',
                'event=keypress keyCode=67 modifier=0 / 0 / 1 / 0',
                'event=keyup keyCode=67 modifier=0 / 0 / 1 / 0',
                'event=keyup keyCode=16 modifier=0 / 0 / 0 / 0'
            )),
            'meta' => array('meta', 'd', array( // d = 100 D = 68
                'event=keydown keyCode=224 modifier=0 / 0 / 0 / 1',
                'event=keydown keyCode=68 modifier=0 / 0 / 0 / 1',
                'event=keypress keyCode=100 modifier=0 / 0 / 0 / 1',
                'event=keyup keyCode=68 modifier=0 / 0 / 0 / 1',
                'event=keyup keyCode=224 modifier=0 / 0 / 0 / 0'
            ))
        );
    }
}
