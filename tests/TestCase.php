<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\WebAssert;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private const DRIVER_CONFIG_FACTORY_KEY = 'driver_config_factory';
    private const MINK_SESSION_KEY = 'sess';

    /**
     * Mink session manager.
     *
     * @var Mink|null
     */
    private static $mink = null;

    /**
     * @var AbstractConfig|null
     */
    private static $config = null;

    /**
     * @beforeClass
     *
     * @return void
     */
    public static function prepareSession()
    {
        if (null === self::$mink) {
            $session = new Session(self::getConfig()->createDriver());
            self::$mink = new Mink([self::MINK_SESSION_KEY => $session]);
        }
    }

    /**
     * @throws \UnexpectedValueException
     */
    private static function createConfig(): AbstractConfig
    {
        $config = call_user_func($GLOBALS[self::DRIVER_CONFIG_FACTORY_KEY]);
        if (!$config instanceof AbstractConfig) {
            throw new \UnexpectedValueException(sprintf(
                'The "%s" global variable must return a %s.',
                self::DRIVER_CONFIG_FACTORY_KEY,
                AbstractConfig::class
            ));
        }
        return $config;
    }

    private static function getConfig(): AbstractConfig
    {
        return self::$config ?? (self::$config = self::createConfig());
    }

    /**
     * @before
     *
     * @return void
     */
    protected function checkSkippedTest()
    {
        if (null !== $message = self::getConfig()->skipMessage(get_class($this), $this->getName(false))) {
            $this->markTestSkipped($message);
        }
    }

    /**
     * @after
     * @return void
     */
    protected function resetSessions()
    {
        if (null !== self::$mink) {
            self::$mink->resetSessions();
        }
    }

    /**
     * Returns session.
     *
     * @return Session
     */
    protected function getSession()
    {
        assert(self::$mink !== null);
        return self::$mink->getSession(self::MINK_SESSION_KEY);
    }

    /**
     * Returns assert session.
     *
     * @return WebAssert
     */
    protected function getAssertSession()
    {
        assert(self::$mink !== null);
        return self::$mink->assertSession(self::MINK_SESSION_KEY);
    }

    /**
     * @param string $id
     *
     * @return NodeElement
     */
    protected function findById($id)
    {
        return $this->getAssertSession()->elementExists('named', ['id', $id]);
    }

    /**
     * Creates a new driver instance.
     *
     * This driver is not associated to a session. It is meant to be used for tests on the driver
     * implementation itself rather than test using the Mink API.
     *
     * @return \Behat\Mink\Driver\DriverInterface
     */
    protected function createDriver()
    {
        return self::getConfig()->createDriver();
    }

    /**
     * Map remote file path.
     *
     * @param string $file File path
     *
     * @return string
     */
    protected function mapRemoteFilePath($file)
    {
        $realPath = realpath($file);

        if (false !== $realPath) {
            $file = $realPath;
        }

        return self::getConfig()->mapRemoteFilePath($file);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function pathTo($path)
    {
        return rtrim(self::getConfig()->getWebFixturesUrl(), '/') . '/' . ltrim($path, '/');
    }

    /**
     * Waits for a condition to be true, considering than it is successful for drivers not supporting wait().
     *
     * @param int $time
     * @param string $condition A JS condition to evaluate
     *
     * @return bool
     *
     * @see \Behat\Mink\Session::wait()
     */
    protected function safePageWait($time, $condition)
    {
        try {
            return $this->getSession()->wait($time, $condition);
        } catch (UnsupportedDriverActionException $e) {
            return true;
        }
    }
}
