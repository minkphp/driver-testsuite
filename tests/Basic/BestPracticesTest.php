<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;
use Behat\Mink\Driver\CoreDriver;

/**
 * This testcase ensures that the driver implementation follows recommended practices for drivers.
 */
class BestPracticesTest extends TestCase
{
    public function testExtendsCoreDriver(): \Behat\Mink\Driver\DriverInterface
    {
        $driver = $this->createDriver();

        $this->assertInstanceOf(CoreDriver::class, $driver);

        return $driver;
    }

    /**
     * @depends testExtendsCoreDriver
     *
     * @return void
     */
    public function testImplementFindXpath(): void
    {
        $driver = $this->createDriver();

        $this->assertNotImplementMethod('find', $driver, 'The driver should overwrite `findElementXpaths` rather than `find` for forward compatibility with Mink 2.');
        $this->assertImplementMethod('findElementXpaths', $driver, 'The driver must be able to find elements.');
        $this->assertNotImplementMethod('setSession', $driver, 'The driver should not deal with the Session directly for forward compatibility with Mink 2.');
    }

    /**
     * @dataProvider provideRequiredMethods
     *
     * @return void
     */
    public function testImplementBasicApi(string $method): void
    {
        $driver = $this->createDriver();

        $this->assertImplementMethod($method, $driver, 'The driver is unusable when this method is not implemented.');
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: 'start'}, 1: array{0: 'isStarted'}, 2: array{0: 'stop'}, 3: array{0: 'reset'}, 4: array{0: 'visit'}, 5: array{0: 'getCurrentUrl'}, 6: array{0: 'getContent'}, 7: array{0: 'click'}}
     */
    public function provideRequiredMethods(): array
    {
        return array(
            array('start'),
            array('isStarted'),
            array('stop'),
            array('reset'),
            array('visit'),
            array('getCurrentUrl'),
            array('getContent'),
            array('click'),
        );
    }

    private function assertImplementMethod(string $method, \Behat\Mink\Driver\DriverInterface $object, string $reason = ''): void
    {
        $ref = new \ReflectionClass(get_class($object));
        $refMethod = $ref->getMethod($method);

        $message = sprintf('The driver should implement the `%s` method.', $method);

        if ('' !== $reason) {
            $message .= ' '.$reason;
        }

        $this->assertNotSame('Behat\Mink\Driver\CoreDriver', $refMethod->getDeclaringClass()->name, $message);
    }

    private function assertNotImplementMethod(string $method, \Behat\Mink\Driver\DriverInterface $object, string $reason = ''): void
    {
        $ref = new \ReflectionClass(get_class($object));
        $refMethod = $ref->getMethod($method);

        $message = sprintf('The driver should not implement the `%s` method.', $method);

        if ('' !== $reason) {
            $message .= ' '.$reason;
        }

        $this->assertSame('Behat\Mink\Driver\CoreDriver', $refMethod->getDeclaringClass()->name, $message);
    }
}
