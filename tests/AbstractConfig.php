<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Driver\DriverInterface;

abstract class AbstractConfig
{
    /**
     * Creates driver instance.
     *
     * @return DriverInterface
     */
    abstract public function createDriver();

    /**
     * Map remote file path.
     *
     * @param string $file File path
     *
     * @return string
     */
    public function mapRemoteFilePath($file)
    {
        $testMachineBasePath = $this->getEnvVar('TEST_MACHINE_BASE_PATH');
        $driverMachineBasePath = $this->getEnvVar('DRIVER_MACHINE_BASE_PATH');

        if (null === $testMachineBasePath || null === $driverMachineBasePath) {
            return $file;
        }

        $pattern = '/^' . preg_quote($testMachineBasePath, '/') . '/';

        return preg_replace($pattern, $driverMachineBasePath, $file, 1) ?? $file;
    }

    /**
     * Gets the base url to the fixture folder.
     *
     * @return string
     */
    public function getWebFixturesUrl()
    {
        // Host used by default by mink-test-server
        return $this->getEnvVar('WEB_FIXTURES_HOST') ?? 'http://localhost:8002';
    }

    /**
     * Gets a variable from the environment, falling back to the $_SERVER one (e.g. a "<server>" in PHPUnit config).
     *
     * The environment goes first, because PHPUnit's "<server>" overwrites the $_SERVER value coming from it.
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function getEnvVar($name)
    {
        $value = getenv($name);

        if (false === $value) {
            $value = $_SERVER[$name] ?? null;
        }

        return \is_string($value) ? $value : null;
    }

    /**
     * @param string $testCase The name of the TestCase class
     * @param string $test The name of the test method
     *
     * @return string|null A message explaining why the test should be skipped, or null to run the test
     */
    public function skipMessage($testCase, $test)
    {
        if (!$this->supportsCss() && 0 === strpos($testCase, 'Behat\Mink\Tests\Driver\Css\\')) {
            return 'This driver does not support CSS.';
        }

        if (!$this->supportsJs() && 0 === strpos($testCase, 'Behat\Mink\Tests\Driver\Js\\')) {
            return 'This driver does not support JavaScript.';
        }

        return null;
    }

    /**
     * Whether the JS tests should run or no.
     *
     * @return bool
     */
    protected function supportsJs()
    {
        return true;
    }

    /**
     * Whether the CSS tests should run or no.
     *
     * @return bool
     */
    protected function supportsCss()
    {
        return false;
    }
}
