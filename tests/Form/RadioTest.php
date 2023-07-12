<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Tests\Driver\TestCase;

final class RadioTest extends TestCase
{
    /**
     * @before
     */
    protected function visitPage(): void
    {
        $this->getSession()->visit($this->pathTo('radio.html'));
    }

    public function testIsChecked(): void
    {
        $option = $this->findById('first');
        $option2 = $this->findById('second');

        $this->assertTrue($option->isChecked());
        $this->assertFalse($option2->isChecked());

        $option2->selectOption('updated');

        $this->assertFalse($option->isChecked());
        $this->assertTrue($option2->isChecked());
    }

    public function testSelectOption(): void
    {
        $option = $this->findById('first');

        $this->assertEquals('set', $option->getValue());

        $option->selectOption('updated');

        $this->assertEquals('updated', $option->getValue());

        $option->selectOption('set');

        $this->assertEquals('set', $option->getValue());
    }

    public function testValueIsNullIfNoSelectedOption(): void
    {
        $option = $this->findById('empty');

        self::assertNull($option->getValue());
    }

    public function testSetValue(): void
    {
        $option = $this->findById('first');

        $this->assertEquals('set', $option->getValue());

        $option->setValue('updated');

        $this->assertEquals('updated', $option->getValue());
        $this->assertFalse($option->isChecked());
    }

    public function testSameNameInMultipleForms(): void
    {
        $option1 = $this->findById('reused_form1');
        $option2 = $this->findById('reused_form2');

        $this->assertEquals('test2', $option1->getValue());
        $this->assertEquals('test3', $option2->getValue());

        $option1->selectOption('test');

        $this->assertEquals('test', $option1->getValue());
        $this->assertEquals('test3', $option2->getValue());
    }

    /**
     * @see https://github.com/Behat/MinkSahiDriver/issues/32
     */
    public function testSetValueXPathEscaping(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/advanced_form.html'));
        $page = $session->getPage();

        $sex = $page->find('xpath', '//*[@name = "sex"]' . "\n|\n" . '//*[@id = "sex"]');
        $this->assertNotNull($sex, 'xpath with line ending works');

        $sex->setValue('m');
        $this->assertEquals('m', $sex->getValue(), 'no double xpath escaping during radio button value change');
    }

    public function testSetArrayValue(): void
    {
        $option = $this->findById('first');

        $this->expectException(DriverException::class);
        $option->setValue(['bad']);
    }

    /**
     * @dataProvider provideBooleanValues
     */
    public function testSetBooleanValue(bool $value): void
    {
        $option = $this->findById('first');

        $this->expectException(DriverException::class);
        $option->setValue($value);
    }

    public static function provideBooleanValues(): iterable
    {
        yield [true];
        yield [false];
    }
}
