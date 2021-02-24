<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

/**
 * @group slow
 */
class ErrorHandlingTest extends TestCase
{
    const NOT_FOUND_XPATH = '//html/./invalid';

    const NOT_FOUND_EXCEPTION = 'Exception';

    const INVALID_EXCEPTION = 'Exception';

    public function testVisitErrorPage()
    {
        $this->getSession()->visit($this->pathTo('/500.php'));

        $this->assertStringContainsString(
            'Sorry, a server error happened',
            $this->getSession()->getPage()->getContent(),
            'Drivers allow loading pages with a 500 status code'
        );
    }

    public function testCheckInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectInvalidException();
        $this->getSession()->getDriver()->check($element->getXpath());
    }

    public function testCheckNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->check(self::NOT_FOUND_XPATH);
    }

    public function testUncheckInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectInvalidException();
        $this->getSession()->getDriver()->uncheck($element->getXpath());
    }

    public function testUncheckNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->uncheck(self::NOT_FOUND_XPATH);
    }

    public function testSelectOptionInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectInvalidException();
        $this->getSession()->getDriver()->selectOption($element->getXpath(), 'test');
    }

    public function testSelectOptionNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->selectOption(self::NOT_FOUND_XPATH, 'test');
    }

    public function testAttachFileInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('user-name');

        $this->expectInvalidException();
        $this->getSession()->getDriver()->attachFile($element->getXpath(), __FILE__);
    }

    public function testAttachFileNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->attachFile(self::NOT_FOUND_XPATH, __FILE__);
    }

    public function testSubmitFormInvalidElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $element = $this->findById('core');

        $this->expectInvalidException();
        $this->getSession()->getDriver()->submitForm($element->getXpath());
    }

    public function testSubmitFormNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->submitForm(self::NOT_FOUND_XPATH);
    }

    public function testGetTagNameNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->getTagName(self::NOT_FOUND_XPATH);
    }

    public function testGetTextNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->getText(self::NOT_FOUND_XPATH);
    }

    public function testGetHtmlNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->getHtml(self::NOT_FOUND_XPATH);
    }

    public function testGetOuterHtmlNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->getOuterHtml(self::NOT_FOUND_XPATH);
    }

    public function testGetValueNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->getValue(self::NOT_FOUND_XPATH);
    }

    public function testSetValueNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->setValue(self::NOT_FOUND_XPATH, 'test');
    }

    public function testIsSelectedNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->isSelected(self::NOT_FOUND_XPATH);
    }

    public function testIsCheckedNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->isChecked(self::NOT_FOUND_XPATH);
    }

    public function testIsVisibleNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->isVisible(self::NOT_FOUND_XPATH);
    }

    public function testClickNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->click(self::NOT_FOUND_XPATH);
    }

    public function testDoubleClickNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->doubleClick(self::NOT_FOUND_XPATH);
    }

    public function testRightClickNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->rightClick(self::NOT_FOUND_XPATH);
    }

    public function testGetAttributeNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->getAttribute(self::NOT_FOUND_XPATH, 'id');
    }

    public function testMouseOverNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->mouseOver(self::NOT_FOUND_XPATH);
    }

    public function testFocusNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->focus(self::NOT_FOUND_XPATH);
    }

    public function testBlurNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->blur(self::NOT_FOUND_XPATH);
    }

    public function testKeyPressNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->keyPress(self::NOT_FOUND_XPATH, 'a');
    }

    public function testKeyDownNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->keyDown(self::NOT_FOUND_XPATH, 'a');
    }

    public function testKeyUpNotFoundElement()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->expectNotFoundException();
        $this->getSession()->getDriver()->keyUp(self::NOT_FOUND_XPATH, 'a');
    }

    private function expectNotFoundException()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException(self::NOT_FOUND_EXCEPTION);
        } else {
            // BC with PHPUnit 4 used for PHP 5.5 and older
            $this->setExpectedException(self::NOT_FOUND_EXCEPTION);
        }
    }

    private function expectInvalidException()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException(self::INVALID_EXCEPTION);
        } else {
            // BC with PHPUnit 4 used for PHP 5.5 and older
            $this->setExpectedException(self::INVALID_EXCEPTION);
        }
    }
}
