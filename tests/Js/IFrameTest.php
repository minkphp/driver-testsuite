<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

/**
 * Behaviour of JavaScript execution and mouse interaction while an iframe is selected.
 *
 * @see \Behat\Mink\Tests\Driver\Basic\IFrameTest for reading content from an iframe.
 */
final class IFrameTest extends TestCase
{
    public function testEvaluateScriptRunsInSelectedIFrame(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/iframe.html'));

        $this->assertStringEndsWith('/iframe.html', $session->evaluateScript('window.location.href'));

        $session->switchToIFrame('subframe_by_name');
        $this->assertStringEndsWith('/iframe_inner.html', $session->evaluateScript('window.location.href'));
        $this->assertSame('iFrame div text', $session->evaluateScript('document.getElementById("text").textContent.trim()'));

        $session->switchToIFrame();
        $this->assertStringEndsWith('/iframe.html', $session->evaluateScript('window.location.href'));
        $this->assertSame('Main window div text', $session->evaluateScript('document.getElementById("text").textContent.trim()'));
    }

    public function testExecuteScriptRunsInSelectedIFrame(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/iframe.html'));

        $session->switchToIFrame('subframe_by_name');
        $session->executeScript('window.executed_in = "iframe";');
        $this->assertSame('iframe', $session->evaluateScript('window.executed_in'));

        $session->switchToIFrame();
        $this->assertNull($session->evaluateScript('window.executed_in || null'));
    }

    public function testClickInIFrameTriggersChangeEvent(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/iframe_element_change_detector.html'));
        $session->switchToIFrame('change_detector_frame');

        $page = $session->getPage();
        $checkbox = $this->findById('the-unchecked-checkbox');
        $resultBeforeClick = $page->findById('the-unchecked-checkbox-result');
        $this->assertNull($resultBeforeClick);

        $checkbox->click();

        $result = $page->findById('the-unchecked-checkbox-result');
        $this->assertNotNull($result, 'change event was not observed for a click inside the iframe');
        $this->assertSame('1', $result->getText());
    }

    public function testClickInIFrameTriggersMouseEvents(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/iframe_element_change_detector.html'));
        $session->switchToIFrame('change_detector_frame');

        $session->executeScript(<<<'JS'
window.mouse_events = [];
var checkbox = document.getElementById('the-unchecked-checkbox');
['mousedown', 'mouseup', 'click'].forEach(function (name) {
    checkbox.addEventListener(name, function (event) {
        window.mouse_events.push(event.type);
    });
});
JS
        );

        $this->findById('the-unchecked-checkbox')->click();

        $this->assertSame('mousedown,mouseup,click', $session->evaluateScript('window.mouse_events.join(",")'));
    }
}
