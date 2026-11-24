<?php

namespace Behat\Mink\Tests\Driver\Form;

use Behat\Mink\Tests\Driver\TestCase;

final class FileUploadTest extends TestCase
{
    public function testAttachFileAvailableToBrowser(): void
    {
        $this->assertFileUploaded(
            $this->mapRemoteFilePath(self::WEB_FIXTURES_DIR . '/some_file.txt'),
            'some_file.txt',
            '1 uploaded file'
        );
    }

    public function testAttachFileOnlyOnTestMachine(): void
    {
        // The path isn't mapped: when the browser runs on another machine, the driver must get the file there itself.
        $path = tempnam(sys_get_temp_dir(), 'mink_upload_');
        $this->assertNotFalse($path, 'Could not create a temporary file.');
        $content = 'Uploaded from the test machine: ' . bin2hex(random_bytes(8));

        try {
            file_put_contents($path, $content);
            $this->assertFileUploaded($path, basename($path), $content);
        } finally {
            unlink($path);
        }
    }

    private function assertFileUploaded(string $path, string $expectedName, string $expectedContent): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/file_upload_form.html'));

        $webAssert = $this->getAssertSession();
        $webAssert->fieldExists('the_file')->attachFile($path);
        $session->getPage()->pressButton('Upload');
        $this->safePageWait(5000, 'document.title === "File upload result"');

        $this->assertSame($expectedName, $webAssert->elementExists('css', '#name')->getText());
        $this->assertSame($expectedContent, $webAssert->elementExists('css', '#content')->getText());
    }
}
