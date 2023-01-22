Mink Driver testsuite
=====================

This is the common testsuite for Mink drivers to ensure consistency among implementations.

Usage
-----

The testsuite of a driver should be based as follow:

```json
{
    "require": {
        "behat/mink": "^1.9"
    },

    "require-dev": {
        "mink/driver-testsuite": "dev-master",
        "phpunit/phpunit": "^8.5.22 || ^9.5.11"
    },

    "autoload-dev": {
        "psr-4": {
            "Acme\\MyDriver\\Tests\\": "tests"
        }
    }
}
```

The PHPUnit config should look like this:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<phpunit colors="true" bootstrap="vendor/autoload.php">
    <php>
        <var name="driver_config_factory" value="Acme\MyDriver\Tests\Config::getInstance" />
    </php>

    <testsuites>
        <testsuite name="Functional tests">
            <directory>vendor/mink/driver-testsuite/tests</directory>
        </testsuite>
        <!-- if needed to add more tests -->
        <testsuite name="Driver tests">
            <directory>./tests/</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory>./src</directory>
        </whitelist>
    </filter>

    <listeners>
        <listener class="Symfony\Bridge\PhpUnit\SymfonyTestsListener"/>
    </listeners>
</phpunit>
```

Then create the driver config for the testsuite:

```php
// tests/Config.php

namespace Acme\MyDriver\Tests;

use Behat\Mink\Tests\Driver\AbstractConfig;

class Config extends AbstractConfig
{
    /**
     * Creates an instance of the config.
     *
     * This is the callable registered as a php variable in the phpunit.xml config file.
     * It could be outside the class but this is convenient.
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * Creates driver instance.
     *
     * @return \Behat\Mink\Driver\DriverInterface
     */
    public function createDriver()
    {
        return new \Acme\MyDriver\MyDriver();
    }
}
```

Some other methods are available in the AbstractConfig which can be overwritten to adapt the testsuite to
the needs of the driver (skipping some tests for instance).

Running tests
-------------

Before running tests, you need to start the webserver exposing the web fixtures (unless the driver does
not perform real HTTP requests). This is done using this command:

```bash
vendor/bin/mink-test-server
```

To stop the server at the end of tests, cancel the command.

> Note: this command requires Bash. If you are on Windows, use either GitBash or Cygwin (or another
> equivalent tool) to launch it.
>
> This command also requires PHP 5.4+ to be able to use the builtin webserver. If the PHP version available
> in the PATH is a different one, use the `MINK_PHP_BIN` env variable to select a different PHP runtime.

You can now run tests for your driver with `vendor/bin/phpunit`.
This package installs PHPUnit as a dependency to ensure that a version of PHPUnit compatible with the testsuite is used.

Adding Driver-specific Tests
----------------------------

When adding extra test cases specific to the driver, either use your own namespace or put them in the
`Behat\Mink\Tests\Driver\Custom` sub-namespace to ensure that you will not create conflicts with test cases
added in the driver testsuite in the future.
When the driver has its own tests, it is recommended to add the dev requirement on `phpunit/phpunit` to
ensure that the tests are compatible with phpunit even if driver-testsuite adds support for newer versions.

