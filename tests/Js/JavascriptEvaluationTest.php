<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

final class JavascriptEvaluationTest extends TestCase
{
    /**
     * Tests, that `wait` method returns check result after exit.
     */
    public function testWaitReturnValue(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));

        $found = $this->getSession()->wait(5000, '$("#draggable").length === 1');
        $this->assertTrue($found);
    }

    public function testWaitReturnValueAlwaysBoolean(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));

        $found = $this->getSession()->wait(5000, '$("#draggable").length');
        $this->assertTrue($found);
    }

    public function testWait(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));

        $waitable = $this->findById('waitable');

        $waitable->click();
        $this->getSession()->wait(3000, '$("#waitable").has("div").length > 0');
        $this->assertEquals('arrived', $this->getAssertSession()->elementExists('css', '#waitable > div')->getText());

        $waitable->click();
        $this->getSession()->wait(3000, 'false');
        $this->assertEquals('timeout', $this->getAssertSession()->elementExists('css', '#waitable > div')->getText());
    }

    /**
     * @dataProvider provideExecutedScript
     */
    public function testExecuteScript(string $script): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->getSession()->executeScript($script);

        sleep(1);

        $heading = $this->getAssertSession()->elementExists('css', 'h1');
        $this->assertEquals('Hello world', $heading->getText());
    }

    public static function provideExecutedScript(): iterable
    {
        return [
            ['document.querySelector("h1").textContent = "Hello world"'],
            ['document.querySelector("h1").textContent = "Hello world";'],
            ['function () {document.querySelector("h1").textContent = "Hello world";}()'],
            ['function () {document.querySelector("h1").textContent = "Hello world";}();'],
            ['(function () {document.querySelector("h1").textContent = "Hello world";})()'],
            ['(function () {document.querySelector("h1").textContent = "Hello world";})();'],
        ];
    }

    /**
     * @dataProvider provideEvaluatedScript
     */
    public function testEvaluateJavascript(string $script): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->assertSame(2, $this->getSession()->evaluateScript($script));
    }

    public static function provideEvaluatedScript(): iterable
    {
        return [
            ['1 + 1'],
            ['1 + 1;'],
            ['return 1 + 1'],
            ['return 1 + 1;'],
            ['function () {return 1+1;}()'],
            ['(function () {return 1+1;})()'],
            ['return function () { return 1+1;}()'],
            ['return (function () {return 1+1;})()'],
        ];
    }
}
