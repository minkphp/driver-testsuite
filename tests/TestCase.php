<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Tests\Driver\Util\TestCaseInvalidStateException;
use Behat\Mink\WebAssert;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use OnNotSuccessfulTrait;

    /**
     * Mink session manager.
     *
     * @var Mink
     */
    private static $mink;

    /**
     * @var AbstractConfig
     */
    private static $config;


    /**
     * @beforeClass
     *
     * @return void
     */
    public static function prepareSession()
    {
        if (null === self::$mink) {
            $session = new Session(self::getConfig()->createDriver());
            self::$mink = new Mink(array('sess' => $session));
        }

        parent::setUpBeforeClass();
    }

    /**
     * Inherited from SetUpTearDownTrait
     */
    protected function doSetUp()
    {
        if (null !== $message = self::getConfig()->skipMessage(get_class($this), $this->getName(false))) {
            self::markTestSkipped($message);
        }

        parent::setUp();
    }

    /**
     * Inherited from SetUpTearDownTrait
     */
    private function doTearDown()
    {
        if (null !== self::$mink) {
            self::$mink->resetSessions();
        }

        parent::tearDown();
    }

    /**
     * @return AbstractConfig
     *
     * @throws \UnexpectedValueException if the global driver_config_factory returns an invalid object
     */
    private static function getConfig()
    {
        if (null === self::$config) {
            self::$config = call_user_func($GLOBALS['driver_config_factory']);

            if (!self::$config instanceof AbstractConfig) {
                throw new \UnexpectedValueException('The "driver_config_factory" global variable must return a \Behat\Mink\Tests\Driver\AbstractConfig.');
            }
        }

        return self::$config;
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

    protected function onNotSuccessfulTest(\Throwable $e): void
    {
        if ($e instanceof UnsupportedDriverActionException) {
            @trigger_error(sprintf('Relying on catching "UnsupportedDriverActionException" to mark tests as skipped is deprecated. The test "%s::%s" should be marked as skipped through the test config.', get_class($this), $this->getName(false)), E_USER_DEPRECATED);
            $this->markTestSkipped($e->getMessage());
        }

        parent::onNotSuccessfulTest($e);
    }

    /**
     * Returns session.
     *
     * @return Session
     */
    protected function getSession()
    {
        if (!self::$mink) {
            throw new TestCaseInvalidStateException('getSession was called before setUpBeforeClass');
        }

        return self::$mink->getSession('sess');
    }

    /**
     * Returns assert session.
     *
     * @return WebAssert
     */
    protected function getAssertSession()
    {
        if (!self::$mink) {
            throw new TestCaseInvalidStateException('getSession was called before setUpBeforeClass');
        }

        return self::$mink->assertSession('sess');
    }

    /**
     * @param string $id
     *
     * @return NodeElement
     */
    protected function findById($id): NodeElement
    {
        return $this->getAssertSession()->elementExists('named', array('id', $id));
    }

    /**
     * Creates a new driver instance.
     *
     * This driver is not associated to a session. It is meant to be used for tests on the driver
     * implementation itself rather than test using the Mink API.
     *
     * @return DriverInterface
     */
    protected function createDriver(): DriverInterface
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
        return rtrim(self::getConfig()->getWebFixturesUrl(), '/').'/'.ltrim($path, '/');
    }

    /**
     * Waits for a condition to be true, considering than it is successful for drivers not supporting wait().
     *
     * @param int    $time
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
