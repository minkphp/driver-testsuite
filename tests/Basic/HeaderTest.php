<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class HeaderTest extends TestCase
{
    /**
     * test referrer.
     *
     * @group issue130
     *
     * @return void
     */
    public function testIssue130(): void
    {
        $this->getSession()->visit($this->pathTo('/issue130.php?p=1'));
        $page = $this->getSession()->getPage();

        $page->clickLink('Go to 2');

        // usleep is required for firefox
        // firefox does not wait for page load as chrome as we may get StaleElementReferenceException
        usleep(500000);

        $this->assertEquals($this->pathTo('/issue130.php?p=1'), $page->getText());
    }

    public function testHeaders(): void
    {
        $this->getSession()->setRequestHeader('Accept-Language', 'fr');
        $this->getSession()->visit($this->pathTo('/headers.php'));

        $this->assertStringContainsString('HTTP_ACCEPT_LANGUAGE = `fr`', $this->getSession()->getPage()->getContent());
    }

    public function testSetUserAgent(): void
    {
        $session = $this->getSession();

        $session->setRequestHeader('user-agent', 'foo bar');
        $session->visit($this->pathTo('/headers.php'));
        $this->assertStringContainsString('HTTP_USER_AGENT = `foo bar`', $session->getPage()->getContent());
    }

    public function testResetHeaders(): void
    {
        $session = $this->getSession();

        $session->setRequestHeader('X-Mink-Test', 'test');
        $session->visit($this->pathTo('/headers.php'));

        $this->assertStringContainsString(
            'HTTP_X_MINK_TEST = `test`',
            $session->getPage()->getContent(),
            'The custom header should be sent'
        );

        $session->reset();
        $session->visit($this->pathTo('/headers.php'));

        $this->assertStringNotContainsString(
            'HTTP_X_MINK_TEST = `test`',
            $session->getPage()->getContent(),
            'The custom header should not be sent after resetting'
        );
    }

    public function testResponseHeaders(): void
    {
        $this->getSession()->visit($this->pathTo('/response_headers.php'));

        /** @psalm-var array<string, string> */
        $headers = $this->getSession()->getResponseHeaders();

        $lowercasedHeaders = array();
        foreach ($headers as $name => $value) {
            $lowercasedHeaders[str_replace('_', '-', strtolower($name))] = $value;
        }

        $this->assertArrayHasKey('x-mink-test', $lowercasedHeaders);
    }
}
