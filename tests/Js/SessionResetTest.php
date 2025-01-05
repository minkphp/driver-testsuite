<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

final class SessionResetTest extends TestCase
{
    /**
     * @dataProvider initialWindowNameDataProvider
     */
    public function testSessionResetClosesWindows(?string $initialWindowName): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/window.html'));

        if (null !== $initialWindowName) {
            $session->executeScript('window.name = "' . $initialWindowName . '";');
        }

        $page = $session->getPage();

        $page->clickLink('Popup #1');
        $page->clickLink('Popup #2');

        $expectedInitialWindowName = $session->evaluateScript('window.name');

        $windowNames = $session->getWindowNames();
        $this->assertCount(3, $windowNames, 'Incorrect opened window count.'); // Initial window + 2 opened popups.

        $session->reset();

        $windowNames = $session->getWindowNames();
        $this->assertCount(1, $windowNames, 'Incorrect opened window count.'); // Initial window only.

        $actualInitialWindowName = $session->evaluateScript('window.name');
        $this->assertEquals($expectedInitialWindowName, $actualInitialWindowName, 'Not inside an initial window.');
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function initialWindowNameDataProvider(): iterable
    {
        yield 'no name' => [null];
        yield 'non-empty name' => ['initial-window'];
    }

    /**
     * @after
     */
    protected function resetSessions()
    {
        $session = $this->getSession();

        // Stop the session instead of resetting, because resetting behavior is being tested.
        if ($session->isStarted()) {
            $session->stop();
        }

        parent::resetSessions();
    }
}
