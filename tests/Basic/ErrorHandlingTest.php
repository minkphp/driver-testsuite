<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

/**
 * @group slow
 */
class ErrorHandlingTest extends TestCase
{
    const NOT_FOUND_XPATH = '//html/./invalid';

    const NOT_FOUND_EXCEPTION = \Exception::class;

    const INVALID_EXCEPTION = \Exception::class;

    public function testVisitErrorPage(): void
    {
        $this->getSession()->visit($this->pathTo('/500.php'));

        $this->assertStringContainsString(
            'Sorry, a server error happened',
            $this->getSession()->getPage()->getContent(),
            'Drivers allow loading pages with a 500 status code'
        );
    }

    public function testCheckInvalidElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectException(self::INVALID_EXCEPTION);
        $this->getSession()->getDriver()->check($element->getXpath());
    }

    public function testCheckNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->check(self::NOT_FOUND_XPATH);
    }

    public function testUncheckInvalidElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectException(self::INVALID_EXCEPTION);
        $this->getSession()->getDriver()->uncheck($element->getXpath());
    }

    public function testUncheckNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->uncheck(self::NOT_FOUND_XPATH);
    }

    public function testSelectOptionInvalidElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectException(self::INVALID_EXCEPTION);
        $this->getSession()->getDriver()->selectOption($element->getXpath(), 'test');
    }

    public function testSelectOptionNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->selectOption(self::NOT_FOUND_XPATH, 'test');
    }

    public function testAttachFileInvalidElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectException(self::INVALID_EXCEPTION);
        $this->getSession()->getDriver()->attachFile($element->getXpath(), __FILE__);
    }

    public function testAttachFileNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->attachFile(self::NOT_FOUND_XPATH, __FILE__);
    }

    public function testSubmitFormInvalidElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('core');

        $this->expectException(self::INVALID_EXCEPTION);
        $this->getSession()->getDriver()->submitForm($element->getXpath());
    }

    public function testSubmitFormNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->submitForm(self::NOT_FOUND_XPATH);
    }

    public function testGetTagNameNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->getTagName(self::NOT_FOUND_XPATH);
    }

    public function testGetTextNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->getText(self::NOT_FOUND_XPATH);
    }

    public function testGetHtmlNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->getHtml(self::NOT_FOUND_XPATH);
    }

    public function testGetOuterHtmlNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->getOuterHtml(self::NOT_FOUND_XPATH);
    }

    public function testGetValueNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->getValue(self::NOT_FOUND_XPATH);
    }

    public function testSetValueNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->setValue(self::NOT_FOUND_XPATH, 'test');
    }

    public function testIsSelectedNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->isSelected(self::NOT_FOUND_XPATH);
    }

    public function testIsCheckedNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->isChecked(self::NOT_FOUND_XPATH);
    }

    public function testIsVisibleNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->isVisible(self::NOT_FOUND_XPATH);
    }

    public function testClickNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->click(self::NOT_FOUND_XPATH);
    }

    public function testDoubleClickNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->doubleClick(self::NOT_FOUND_XPATH);
    }

    public function testRightClickNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->rightClick(self::NOT_FOUND_XPATH);
    }

    public function testGetAttributeNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->getAttribute(self::NOT_FOUND_XPATH, 'id');
    }

    public function testMouseOverNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->mouseOver(self::NOT_FOUND_XPATH);
    }

    public function testFocusNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->focus(self::NOT_FOUND_XPATH);
    }

    public function testBlurNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->blur(self::NOT_FOUND_XPATH);
    }

    public function testKeyPressNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->keyPress(self::NOT_FOUND_XPATH, 'a');
    }

    public function testKeyDownNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->keyDown(self::NOT_FOUND_XPATH, 'a');
    }

    public function testKeyUpNotFoundElement(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectException(self::NOT_FOUND_EXCEPTION);
        $this->getSession()->getDriver()->keyUp(self::NOT_FOUND_XPATH, 'a');
    }
}
