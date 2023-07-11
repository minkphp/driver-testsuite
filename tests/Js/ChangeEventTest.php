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
     *
     * @return void
     */
    public function testIssue255(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/issue255.html'));

        $session->getPage()->selectFieldOption('foo_select', 'Option 3');

        $session->wait(2000, '$("#output_foo_select").text() != ""');
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
     *
     * @group change-event-detector
     *
     * @return void
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
        $this->assertNull($page->findById($elementId.'-result'));

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
     * @return string[][]
     *
     * @psalm-return array{'input default': array{0: 'the-input-default', 1: 'from empty', 2: 'from existing'}, 'input text': array{0: 'the-input-text', 1: 'from empty', 2: 'from existing'}, 'input email': array{0: 'the-email', 1: 'from empty', 2: 'from existing'}, textarea: array{0: 'the-textarea', 1: 'from empty', 2: 'from existing'}, file: array{0: 'the-file', 1: string, 2: string}, select: array{0: 'the-select', 1: '30'}, radio: array{0: 'the-radio-m', 1: 'm'}}
     */
    public static function setValueChangeEventDataProvider(): array
    {
        $file1 = __DIR__ . '/../../web-fixtures/file1.txt';
        $file2 = __DIR__ . '/../../web-fixtures/file2.txt';

        return array(
            'input default' => array('the-input-default', 'from empty', 'from existing'),
            'input text' => array('the-input-text', 'from empty', 'from existing'),
            'input email' => array('the-email', 'from empty', 'from existing'),
            'textarea' => array('the-textarea', 'from empty', 'from existing'),
            'file' => array('the-file', $file1, $file2),
            'select' => array('the-select', '30'),
            'radio' => array('the-radio-m', 'm'),
        );
    }

    /**
     * @dataProvider selectOptionChangeEventDataProvider
     *
     * @group change-event-detector
     *
     * @return void
     */
    public function testSelectOptionChangeEvent(string $elementId, string $elementValue): void
    {
        $this->getSession()->visit($this->pathTo('/element_change_detector.html'));
        $page = $this->getSession()->getPage();

        $input = $this->findById($elementId);
        $this->assertNull($page->findById($elementId.'-result'));

        $input->selectOption($elementValue);
        $this->assertElementChangeCount($elementId);
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{select: array{0: 'the-select', 1: '30'}, radio: array{0: 'the-radio-m', 1: 'm'}}
     */
    public static function selectOptionChangeEventDataProvider(): array
    {
        return array(
            'select' => array('the-select', '30'),
            'radio' => array('the-radio-m', 'm'),
        );
    }

    /**
     * @dataProvider checkboxTestWayDataProvider
     *
     * @group change-event-detector
     *
     * @return void
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
     *
     * @group change-event-detector
     *
     * @return void
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

    /** @psalm-return \Generator<int, array{0: bool}, mixed, void> */
    public static function checkboxTestWayDataProvider(): \Generator
    {
        yield [true];
        yield [false];
    }

    private function assertElementChangeCount(string $elementId, string $message = ''): void
    {
        $counterElement = $this->getSession()->getPage()->findById($elementId.'-result');
        $actualCount = null === $counterElement ? 0 : $counterElement->getText();

        $this->assertEquals('1', $actualCount, $message);
    }
}
