<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class ContentTest extends TestCase
{
    public function testOuterHtml(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $element = $this->getAssertSession()->elementExists('css', '.travers');

        $this->assertEquals(
            "<div class=\"travers\">\n            <div class=\"sub\">el1</div>\n" .
            "            <div class=\"sub\">el2</div>\n            <div class=\"sub\">\n" .
            "                <a href=\"some_url\">some <strong>deep</strong> url</a>\n" .
            "            </div>\n        </div>",
            $element->getOuterHtml()
        );
    }

    public function testGetText(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $element = $this->getAssertSession()->elementExists('css', '.get-text-trim');

        /*
         * Tests, these things:
         * - <br> gets replaced with a space
         * - spaces around the text are trimmed
         * - &nbsp; are replaced with " " (non-breakable space) and then with " " (regular space)
         */
        $this->assertEquals('line 2: text inside div line 3:', $element->getText());
    }

    public function testDumpingEmptyElements(): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $element = $this->getAssertSession()->elementExists('css', '#empty');

        $this->assertEquals(
            'An empty <em></em> tag should be rendered with both open and close tags.',
            trim($element->getHtml())
        );
    }

    /**
     * @dataProvider getAttributeDataProvider
     */
    public function testGetAttribute(string $attributeName, ?string $attributeValue): void
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $element = $this->getSession()->getPage()->findById('attr-elem[' . $attributeName . ']');

        $this->assertNotNull($element);
        $this->assertSame($attributeValue, $element->getAttribute($attributeName));
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function getAttributeDataProvider(): iterable
    {
        return [
            ['with-value', 'some-value'],
            ['without-value', ''],
            ['with-empty-value', ''],
            ['with-missing', null],
        ];
    }

    public function testHtmlDecodingNotPerformed(): void
    {
        $session = $this->getSession();
        $webAssert = $this->getAssertSession();
        $session->visit($this->pathTo('/html_decoding.html'));
        $page = $session->getPage();

        $span = $webAssert->elementExists('css', 'span');
        $input = $webAssert->elementExists('css', 'input');

        $expectedHtml = '<span custom-attr="&amp;">some text</span>';
        $this->assertStringContainsString($expectedHtml, $page->getHtml(), '.innerHTML is returned as-is');
        $this->assertStringContainsString($expectedHtml, $page->getContent(), '.outerHTML is returned as-is');

        $this->assertEquals('&', $span->getAttribute('custom-attr'), '.getAttribute value is decoded');
        $this->assertEquals('&', $input->getAttribute('value'), '.getAttribute value is decoded');
        $this->assertEquals('&', $input->getValue(), 'node value is decoded');
    }
}
