<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Tests\Driver\TestCase;

final class CheckboxTest extends TestCase
{
    public function testManipulate(): void
    {
        $this->getSession()->visit($this->pathTo('advanced_form.html'));

        $checkbox = $this->getAssertSession()->fieldExists('agreement');

        $this->assertNull($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());

        $checkbox->check();

        $this->assertEquals('yes', $checkbox->getValue());
        $this->assertTrue($checkbox->isChecked());

        // assert that an already checked checkbox stay checked
        $checkbox->check();
        $this->assertEquals('yes', $checkbox->getValue());
        $this->assertTrue($checkbox->isChecked());

        $checkbox->uncheck();

        $this->assertNull($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());

        // assert that an already unchecked checkbox stay unchecked
        $checkbox->uncheck();
        $this->assertNull($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());
    }

    public function testSetValue(): void
    {
        $this->getSession()->visit($this->pathTo('advanced_form.html'));

        $checkbox = $this->getAssertSession()->fieldExists('agreement');

        $this->assertNull($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());

        $checkbox->setValue(true);

        $this->assertEquals('yes', $checkbox->getValue());
        $this->assertTrue($checkbox->isChecked());

        $checkbox->setValue(false);

        $this->assertNull($checkbox->getValue());
        $this->assertFalse($checkbox->isChecked());
    }

    public function testCheckboxMultiple(): void
    {
        $this->getSession()->visit($this->pathTo('/multicheckbox_form.html'));
        $webAssert = $this->getAssertSession();

        $this->assertEquals('Multicheckbox Test', $webAssert->elementExists('css', 'h1')->getText());

        $updateMail = $webAssert->elementExists('css', '[name="mail_types[]"][value="update"]');
        $spamMail = $webAssert->elementExists('css', '[name="mail_types[]"][value="spam"]');

        $this->assertEquals('update', $updateMail->getValue());
        $this->assertNull($spamMail->getValue());

        $this->assertTrue($updateMail->isChecked());
        $this->assertFalse($spamMail->isChecked());

        $updateMail->uncheck();
        $this->assertFalse($updateMail->isChecked());
        $this->assertFalse($spamMail->isChecked());

        $spamMail->check();
        $this->assertFalse($updateMail->isChecked());
        $this->assertTrue($spamMail->isChecked());
    }

    public function testCheckboxMultipleWithDisabledCheckboxes(): void
    {
        $this->getSession()->visit($this->pathTo('/multicheckbox_disabled_form.html'));
        $webAssert = $this->getAssertSession();

        $disabledChecked = $webAssert->elementExists('css', '[name="mail_types[]"][value="update"]');
        $disabled = $webAssert->elementExists('css', '[name="mail_types[]"][value="spam"]');
        $newsletter = $webAssert->elementExists('css', '[name="mail_types[]"][value="newsletter"]');
        $digest = $webAssert->elementExists('css', '[name="mail_types[]"][value="digest"]');

        $this->assertTrue($disabledChecked->isChecked());
        $this->assertFalse($disabled->isChecked());

        // the enabled checkboxes come after the disabled ones, so a driver that
        // numbers them differently than the browser does manipulates the wrong one
        $this->assertFalse($newsletter->isChecked());
        $this->assertFalse($digest->isChecked());

        $newsletter->check();
        $this->assertTrue($newsletter->isChecked());
        $this->assertFalse($digest->isChecked());

        $digest->check();
        $this->assertTrue($newsletter->isChecked());
        $this->assertTrue($digest->isChecked());

        $newsletter->uncheck();
        $this->assertFalse($newsletter->isChecked());
        $this->assertTrue($digest->isChecked());

        // the disabled ones were not affected
        $this->assertTrue($disabledChecked->isChecked());
        $this->assertFalse($disabled->isChecked());
    }
}
