<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Tests\Driver\TestCase;

class Html5Test extends TestCase
{
    public function testHtml5FormInputAttribute()
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();
        $webAssert = $this->getAssertSession();

        $firstName = $webAssert->fieldExists('first_name');
        $lastName = $webAssert->fieldExists('last_name');

        $this->assertEquals('not set', $lastName->getValue());
        $firstName->setValue('John');
        $lastName->setValue('Doe');

        $this->assertEquals('Doe', $lastName->getValue());

        $page->pressButton('Submit in form');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<'OUT'
  first_name = `John`,
  last_name = `Doe`,
OUT;
            $out = str_replace(["\r", "\r\n", "\n"], \PHP_EOL, $out);
            $this->assertStringContainsString($out, $page->getContent());
            $this->assertStringNotContainsString('other_field', $page->getContent());
        }
    }

    public function testHtml5FormRadioAttribute()
    {
        $this->getSession()->visit($this->pathTo('html5_radio.html'));
        $page = $this->getSession()->getPage();

        $radio = $this->findById('sex_f');
        $otherRadio = $this->findById('sex_invalid');

        $this->assertEquals('f', $radio->getValue());
        $this->assertEquals('invalid', $otherRadio->getValue());

        $radio->selectOption('m');

        $this->assertEquals('m', $radio->getValue());
        $this->assertEquals('invalid', $otherRadio->getValue());

        $page->pressButton('Submit in form');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        $out = <<<'OUT'
  sex = `m`,
OUT;
        $this->assertStringContainsString($out, $page->getContent());
    }

    public function testHtml5FormButtonAttribute()
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();
        $webAssert = $this->getAssertSession();

        $firstName = $webAssert->fieldExists('first_name');
        $lastName = $webAssert->fieldExists('last_name');

        $firstName->setValue('John');
        $lastName->setValue('Doe');

        $page->pressButton('Submit outside form');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<'OUT'
  first_name = `John`,
  last_name = `Doe`,
  submit_button = `test`,
OUT;
            $out = str_replace(["\r", "\r\n", "\n"], \PHP_EOL, $out);
            $this->assertStringContainsString($out, $page->getContent());
        }
    }

    public function testHtml5FormOutside()
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('other_field', 'hello');

        $page->pressButton('Submit separate form');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<'OUT'
  other_field = `hello`,
OUT;
            $this->assertStringContainsString($out, $page->getContent());
            $this->assertStringNotContainsString('first_name', $page->getContent());
        }
    }

    public function testHtml5Types()
    {
        $this->getSession()->visit($this->pathTo('html5_types.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('url', 'http://mink.behat.org/');
        $page->fillField('email', 'mink@example.org');
        $page->fillField('number', '6');
        $page->fillField('search', 'mink');
        $page->fillField('date', '2014-05-19');
        $page->fillField('color', '#ff00aa');

        $page->pressButton('Submit');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        $out = <<<'OUT'
array(
  agreement = `off`,
  color = `#ff00aa`,
  date = `2014-05-19`,
  email = `mink@example.org`,
  number = `6`,
  search = `mink`,
  submit_button = `Submit`,
  url = `http://mink.behat.org/`,
)
no file
OUT;
        $out = str_replace(["\r", "\r\n", "\n"], \PHP_EOL, $out);
        $this->assertStringContainsString($out, $page->getContent());
    }

    public function testHtml5FormAction()
    {
        $this->getSession()->visit($this->pathTo('html5_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'Jimmy');
        $page->pressButton('Submit to basic form');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $this->assertStringContainsString('<title>Basic Form Saving</title>', $page->getContent());
            $this->assertStringContainsString('Firstname: Jimmy', $page->getContent());
        }
    }

    public function testHtml5FormMethod()
    {
        $this->getSession()->visit($this->pathTo('html5_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'Jimmy');
        $page->fillField('last_name', 'Jones');
        $page->pressButton('Submit as GET');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $this->assertEquals(
                $this->pathTo('advanced_form_post.php').'?first_name=Jimmy&last_name=Jones',
                $this->getSession()->getCurrentUrl()
            );
        }
    }

}
