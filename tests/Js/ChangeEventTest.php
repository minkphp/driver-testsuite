<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;
use Behat\Mink\Tests\Driver\Util\FixturesKernel;

/**
 * @group slow
 */
final class ChangeEventTest extends TestCase
{
    /**
     * 'change' event should be fired after selecting an <option> in a <select>.
     *
     * TODO check whether this test is redundant with other change event tests.
     */
    public function testIssue255(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue255.html'));

        $session->getPage()->selectFieldOption('foo_select', 'Option 3');

        $session->wait(2000, '$("#output_foo_select").text() !== ""');
        $this->assertEquals('onChangeSelect', $this->getAssertSession()->elementExists('css', '#output_foo_select')->getText());
    }

    public function testIssue178(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue178.html'));

        $this->findById('source')->setValue('foo');
        $this->assertEquals('foo', $this->findById('target')->getText());
    }

    /**
     * @dataProvider setValueChangeEventDataProvider
     * @group change-event-detector
     */
    public function testSetValueChangeEvent(string $elementId, string $valueForEmpty, string $valueForFilled = ''): void
    {
        if ($elementId === 'the-file') {
            $valueForEmpty = $this->mapRemoteFilePath($valueForEmpty);
            $valueForFilled = $this->mapRemoteFilePath($valueForFilled);
        }

        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $input = $this->findById($elementId);
        $this->assertNull($page->findById($elementId . '-result'));

        // Verify setting value, when control is initially empty.
        $input->setValue($valueForEmpty);
        $this->assertElementChangeCount($elementId, 'initial value setting triggers change event');

        if ($valueForFilled) {
            // Verify setting value, when control already has a value.
            $this->findById('results')->click();
            $input->setValue($valueForFilled);
            $this->assertElementChangeCount($elementId, 'value change triggers change event');
        }
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function setValueChangeEventDataProvider(): iterable
    {
        yield 'input default' => ['the-input-default', 'from empty', 'from existing'];
        yield 'input text' => ['the-input-text', 'from empty', 'from existing'];
        yield 'input email' => ['the-email', 'from empty', 'from existing'];
        yield 'textarea' => ['the-textarea', 'from empty', 'from existing'];
        yield 'file' => ['the-file', self::WEB_FIXTURES_DIR . '/file1.txt', self::WEB_FIXTURES_DIR . '/file2.txt'];
        yield 'select' => ['the-select', '30', ''];
        yield 'radio' => ['the-radio-m', 'm', ''];
    }

    /**
     * @dataProvider selectOptionChangeEventDataProvider
     * @group change-event-detector
     */
    public function testSelectOptionChangeEvent(string $elementId, string $elementValue): void
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $input = $this->findById($elementId);
        $this->assertNull($page->findById($elementId . '-result'));

        $input->selectOption($elementValue);
        $this->assertElementChangeCount($elementId);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function selectOptionChangeEventDataProvider(): iterable
    {
        yield 'select' => ['the-select', '30'];
        yield 'radio' => ['the-radio-m', 'm'];
    }

    /**
     * @dataProvider checkboxTestWayDataProvider
     * @group change-event-detector
     */
    public function testCheckChangeEvent(bool $useSetValue): void
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $checkbox = $this->findById('the-unchecked-checkbox');
        $this->assertNull($page->findById('the-unchecked-checkbox-result'));

        if ($useSetValue) {
            $checkbox->setValue(true);
        } else {
            $checkbox->check();
        }

        $this->assertElementChangeCount('the-unchecked-checkbox');
    }

    /**
     * @dataProvider checkboxTestWayDataProvider
     * @group change-event-detector
     */
    public function testUncheckChangeEvent(bool $useSetValue): void
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $checkbox = $this->findById('the-checked-checkbox');
        $this->assertNull($page->findById('the-checked-checkbox-result'));

        if ($useSetValue) {
            $checkbox->setValue(false);
        } else {
            $checkbox->uncheck();
        }

        $this->assertElementChangeCount('the-checked-checkbox');
    }

    /**
     * @return iterable<array{mixed}>
     */
    public static function checkboxTestWayDataProvider(): iterable
    {
        yield [true];
        yield [false];
    }

    private function assertElementChangeCount(string $elementId, string $message = ''): void
    {
        $counterElement = $this->getSession()->getPage()->findById($elementId . '-result');
        $actualCount = null === $counterElement ? 0 : $counterElement->getText();

        $this->assertEquals('1', $actualCount, $message);
    }
}
