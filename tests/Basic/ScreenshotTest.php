<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;

final class ScreenshotTest extends TestCase
{
    public function testScreenshot(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Testing screenshots requires the GD extension');
        }

        $this->getSession()->visit($this->pathTo('/index.html'));

        $screenshot = $this->getSession()->getScreenshot();
        $this->assertNotEmpty($screenshot);

        $this->assertIsString($screenshot);
        $this->assertFalse(base64_decode($screenshot, true), 'The returned screenshot should not be base64-encoded');

        $img = imagecreatefromstring($screenshot);

        if (false === $img) {
            $this->fail('The screenshot should be a valid image');
        }

        imagedestroy($img);
    }
}
