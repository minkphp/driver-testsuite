<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class BasicAuthTest extends TestCase
{
    /**
     * @dataProvider setBasicAuthDataProvider
     *
     * @return void
     */
    public function testSetBasicAuth(string $user, string $pass, string $pageText): void
    {
        $session = $this->getSession();
        $session->setBasicAuth($user, $pass);
        $session->visit($this->pathTo('/basic_auth.php'));
        $this->assertStringContainsString($pageText, $session->getPage()->getContent());
    }

    /** @psalm-return \Generator<int, array{0: string, 1: string, 2: string}, mixed, void> */
    public static function setBasicAuthDataProvider(): \Generator
    {
        yield ['mink-user', 'mink-password', 'is authenticated'];
        yield ['', '', 'is not authenticated'];
    }

    public function testResetBasicAuth(): void
    {
        $session = $this->getSession();
        $session->setBasicAuth('mink-user', 'mink-password');
        $session->visit($this->pathTo('/basic_auth.php'));
        $this->assertStringContainsString('is authenticated', $session->getPage()->getContent());
        $session->setBasicAuth(false);
        $session->visit($this->pathTo('/headers.php'));
        $this->assertStringNotContainsString('PHP_AUTH_USER', $session->getPage()->getContent());
    }

    public function testBasicAuthInUrl(): void
    {
        if (getenv('BROWSER_NAME') === 'safari') {
            $this->markTestSkipped('\Behat\Mink\Tests\Driver\Basic\BasicAuthTest::testBasicAuthInUrl is skipped due to Safari hangs');
        }

        $session = $this->getSession();

        $url = $this->pathTo('/basic_auth.php');
        $url = str_replace('://', '://mink-user:mink-password@', $url);
        $session->visit($url);
        $this->assertStringContainsString('is authenticated', $session->getPage()->getContent());

        $session->stop();

        $url = $this->pathTo('/basic_auth.php');
        $url = str_replace('://', '://mink-user:wrong@', $url);
        $session->visit($url);

        if (getenv('BROWSER_NAME') === 'firefox') {
            $this->expectException('Facebook\WebDriver\Exception\UnexpectedAlertOpenException');
            $this->expectExceptionMessage('Dismissed user prompt dialog: This site is asking you to sign in.');
        }

        // chrome can access dom when alert/confirm/basic auth
        $this->assertStringContainsString('<html><head></head><body></body></html>', $session->getPage()->getContent());
    }
}
