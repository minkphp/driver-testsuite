<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Tests\Driver\TestCase;

/**
 * Mouse interaction with elements that are outside the viewport.
 */
final class OutsideViewportTest extends TestCase
{
    /**
     * @group mouse-events
     */
    public function testClick(): void
    {
        $element = $this->findOutsideViewport('click-target');

        $this->scrollToTop();
        $element->click();

        $this->assertElementText('clicked', $element);
    }

    /**
     * @group mouse-events
     */
    public function testDoubleClick(): void
    {
        $element = $this->findOutsideViewport('double-click-target');

        $this->scrollToTop();
        $element->doubleClick();

        $this->assertElementText('double clicked', $element);
    }

    /**
     * @group mouse-events
     */
    public function testRightClick(): void
    {
        $element = $this->findOutsideViewport('right-click-target');

        $this->scrollToTop();
        $element->rightClick();

        $this->assertElementText('right clicked', $element);
    }

    /**
     * @group mouse-events
     */
    public function testMouseOver(): void
    {
        $element = $this->findOutsideViewport('mouse-over-target');

        $this->scrollToTop();
        $element->mouseOver();

        $this->assertElementText('mouse overed', $element);
    }

    private function findOutsideViewport(string $id): NodeElement
    {
        $this->getSession()->visit($this->pathTo('/outside_viewport.html'));

        $this->scrollToTop();
        $this->assertTrue(
            $this->getSession()->evaluateScript(
                'document.getElementById("' . $id . '").getBoundingClientRect().top >= window.innerHeight'
            ),
            'The element must be outside the viewport before the test'
        );

        return $this->findById($id);
    }

    private function scrollToTop(): void
    {
        $this->getSession()->executeScript('window.scrollTo(0, 0);');
    }

    private function assertElementText(string $expected, NodeElement $element): void
    {
        $this->scrollToTop();
        $this->assertSame($expected, $element->getText());
    }
}
