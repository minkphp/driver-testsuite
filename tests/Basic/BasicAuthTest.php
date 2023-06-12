<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class BasicAuthTest extends TestCase
{
    /**
     * @dataProvider setBasicAuthDataProvider
     */
    public function testSetBasicAuth(string $user, string $pass, string $pageText)
    {
        $session = $this->getSession();

        $session->setBasicAuth($user, $pass);

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertStringContainsString($pageText, $session->getPage()->getContent());
    }

    public static function setBasicAuthDataProvider()
    {
        return array(
            array('mink-user', 'mink-password', 'is authenticated'),
            array('', '', 'is not authenticated'),
        );
    }

    public function testBasicAuthInUrl()
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

    public function testResetBasicAuth()
    {
        $session = $this->getSession();

        $session->setBasicAuth('mink-user', 'mink-password');

        $session->visit($this->pathTo('/basic_auth.php'));

        $this->assertStringContainsString('is authenticated', $session->getPage()->getContent());

        $session->setBasicAuth(false);

        $session->visit($this->pathTo('/headers.php'));

        $this->assertStringNotContainsString('PHP_AUTH_USER', $session->getPage()->getContent());
    }

    public function testResetWithBasicAuth()
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
