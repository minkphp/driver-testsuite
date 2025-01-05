<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

final class JavascriptEvaluationTest extends TestCase
{
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

    /**
     * @return iterable<array{string}>
     */
    public static function provideExecutedScript(): iterable
    {
        yield ['document.querySelector("h1").textContent = "Hello world"'];
        yield ['document.querySelector("h1").textContent = "Hello world";'];
        yield ['function () {document.querySelector("h1").textContent = "Hello world";}()'];
        yield ['function () {document.querySelector("h1").textContent = "Hello world";}();'];
        yield ['(function () {document.querySelector("h1").textContent = "Hello world";})()'];
        yield ['(function () {document.querySelector("h1").textContent = "Hello world";})();'];
    }

    /**
     * @dataProvider provideEvaluatedScript
     */
    public function testEvaluateJavascript(string $script): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $this->assertSame(2, $this->getSession()->evaluateScript($script));
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideEvaluatedScript(): iterable
    {
        yield ['1 + 1'];
        yield ['1 + 1;'];
        yield ['return 1 + 1'];
        yield ['return 1 + 1;'];
        yield ['function () {return 1+1;}()'];
        yield ['(function () {return 1+1;})()'];
        yield ['return function () { return 1+1;}()'];
        yield ['return (function () {return 1+1;})()'];
    }
}
