<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Driver\CoreDriver;
use Behat\Mink\Tests\Driver\TestCase;

/**
 * This testcase ensures that the driver implementation follows recommended practices for drivers.
 */
final class BestPracticesTest extends TestCase
{
    public function testExtendsCoreDriver(): void
    {
        $driver = $this->createDriver();

        $this->assertInstanceOf(CoreDriver::class, $driver);
    }

    /**
     * @depends testExtendsCoreDriver
     */
    public function testImplementFindXpath(): void
    {
        $driver = $this->createDriver();

        $this->assertMethodIsNotImplemented('find', $driver, 'The driver should overwrite `findElementXpaths` rather than `find` for forward compatibility with Mink 2.');
        $this->assertMethodIsImplemented('findElementXpaths', $driver, 'The driver must be able to find elements.');
        $this->assertMethodIsNotImplemented('setSession', $driver, 'The driver should not deal with the Session directly for forward compatibility with Mink 2.');
    }

    /**
     * @dataProvider provideRequiredMethods
     */
    public function testImplementBasicApi(string $method): void
    {
        $driver = $this->createDriver();

        $this->assertMethodIsImplemented($method, $driver, 'The driver is unusable when this method is not implemented.');
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideRequiredMethods(): iterable
    {
        return [
            ['start'],
            ['isStarted'],
            ['stop'],
            ['reset'],
            ['visit'],
            ['getCurrentUrl'],
            ['getContent'],
            ['click'],
        ];
    }

    private function assertMethodIsImplemented(string $method, object $object, string $reason = ''): void
    {
        $ref = new \ReflectionClass(get_class($object));
        $refMethod = $ref->getMethod($method);

        $message = sprintf('The driver should implement the `%s` method.', $method);

        if ('' !== $reason) {
            $message .= ' ' . $reason;
        }

        $this->assertNotSame(CoreDriver::class, $refMethod->getDeclaringClass()->name, $message);
    }

    private function assertMethodIsNotImplemented(string $method, object $object, string $reason = ''): void
    {
        $ref = new \ReflectionClass(get_class($object));
        $refMethod = $ref->getMethod($method);

        $message = sprintf('The driver should not implement the `%s` method.', $method);

        if ('' !== $reason) {
            $message .= ' ' . $reason;
        }

        $this->assertSame(CoreDriver::class, $refMethod->getDeclaringClass()->name, $message);
    }
}
