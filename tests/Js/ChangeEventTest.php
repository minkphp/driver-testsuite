<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

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

    public static function setValueChangeEventDataProvider(): iterable
    {
        $file1 = __DIR__ . '/../../web-fixtures/file1.txt';
        $file2 = __DIR__ . '/../../web-fixtures/file2.txt';

        return [
            'input default' => ['the-input-default', 'from empty', 'from existing'],
            'input text' => ['the-input-text', 'from empty', 'from existing'],
            'input email' => ['the-email', 'from empty', 'from existing'],
            'textarea' => ['the-textarea', 'from empty', 'from existing'],
            'file' => ['the-file', $file1, $file2],
            'select' => ['the-select', '30'],
            'radio' => ['the-radio-m', 'm'],
        ];
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

    public static function selectOptionChangeEventDataProvider(): iterable
    {
        return [
            'select' => ['the-select', '30'],
            'radio' => ['the-radio-m', 'm'],
        ];
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

    public static function checkboxTestWayDataProvider(): iterable
    {
        return [
            [true],
            [false],
        ];
    }

    private function assertElementChangeCount(string $elementId, string $message = ''): void
    {
        $counterElement = $this->getSession()->getPage()->findById($elementId . '-result');
        $actualCount = null === $counterElement ? 0 : $counterElement->getText();

        $this->assertEquals('1', $actualCount, $message);
    }
}
