<?php

namespace Behat\Mink\Tests\Driver;

use PHPUnit\Runner\Version;

if (class_exists('PHPUnit\Runner\Version') && version_compare(Version::id(), '8.0.0', '>=')) {
    include_once __DIR__ . '/Legacy/SkippingUnsupportedTestCaseV8.php';
} elseif (class_exists('PHPUnit\Runner\Version') && version_compare(Version::id(), '6.0.0', '>=')) {
    include_once __DIR__ . '/Legacy/SkippingUnsupportedTestCaseV6.php';
} elseif (version_compare(\PHPUnit_Runner_Version::id(), '5.0.0', '>=')) {
    include_once __DIR__ . '/Legacy/SkippingUnsupportedTestCaseV5.php';
} else {
    include_once __DIR__ . '/Legacy/SkippingUnsupportedTestCaseV4.php';
}
