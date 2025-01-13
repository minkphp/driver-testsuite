<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class BasicAuthTest extends TestCase
{
    /**
     * @dataProvider setBasicAuthDataProvider
     */
    public function testSetBasicAuth(string $user, string $pass, string $pageText): void
    {
        $session = $this->getSession();

        $session->setBasicAuth($user, $pass);

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertStringContainsString($pageText, $session->getPage()->getContent());
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function setBasicAuthDataProvider(): iterable
    {
        yield 'valid credentials' => ['mink-user', 'mink-password', 'is authenticated'];
        yield 'no credentials' => ['', '', 'is not authenticated'];
    }

    public function testBasicAuthInUrl(): void
    {
        $session = $this->getSession();

        $url = $this->pathTo('/basic_auth.php');
        $url = str_replace('://', '://mink-user:mink-password@', $url);
        $session->visit($url);
        $this->assertStringContainsString('is authenticated', $session->getPage()->getContent());

        $url = $this->pathTo('/basic_auth.php');
        $url = str_replace('://', '://mink-user:wrong@', $url);
        $session->visit($url);
        $this->assertStringContainsString('is not authenticated', $session->getPage()->getContent());
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

    public function testResetWithBasicAuth(): void
    {
        $session = $this->getSession();

        $session->setBasicAuth('mink-user', 'mink-password');

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertStringContainsString('is authenticated', $session->getPage()->getContent());

        $session->reset();

        $session->visit($this->pathTo('/headers.php'));

        $this->assertStringNotContainsString('PHP_AUTH_USER', $session->getPage()->getContent());
    }
}
