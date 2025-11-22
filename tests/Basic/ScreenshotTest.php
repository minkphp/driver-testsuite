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

        $screenShot = $this->getSession()->getScreenshot();

        $this->assertIsString($screenShot);
        $this->assertFalse(base64_decode($screenShot, true), 'The returned screenshot should not be base64-encoded');

        $img = imagecreatefromstring($screenShot);

        if (false === $img) {
            $this->fail('The screenshot should be a valid image');
        }

        if (\PHP_VERSION_ID < 80000) {
            imagedestroy($img);
        }
    }
}
