<?php

namespace Behat\Mink\Tests\Driver\Css;

use Behat\Mink\Tests\Driver\TestCase;

final class HoverTest extends TestCase
{
    /**
     * @group mouse-events
     *
     * @return void
     */
    public function testMouseOverHover(): void
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));

        $this->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $this->findById('action-square')->mouseOver();
        $this->assertActionSquareHeight(200);
    }

    /**
     * @group mouse-events
     *
     * @depends testMouseOverHover
     *
     * @return void
     */
    public function testClickHover(): void
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));

        $this->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $this->findById('action-square')->click();
        $this->assertActionSquareHeight(200);
    }

    /**
     * @group mouse-events
     *
     * @depends testMouseOverHover
     *
     * @return void
     */
    public function testDoubleClickHover(): void
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));

        $this->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $this->findById('action-square')->doubleClick();
        $this->assertActionSquareHeight(200);
    }

    /**
     * @group mouse-events
     *
     * @depends testMouseOverHover
     *
     * @return void
     */
    public function testRightClickHover(): void
    {
        $this->getSession()->visit($this->pathTo('/css_mouse_events.html'));

        $this->findById('reset-square')->mouseOver();
        $this->assertActionSquareHeight(100);

        $this->findById('action-square')->rightClick();
        $this->assertActionSquareHeight(200);
    }

    private function assertActionSquareHeight(int $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->getSession()->evaluateScript("return window.$('#action-square').height();"),
            'Mouse is located over the object when mouse-related action is performed'
        );
    }
}
