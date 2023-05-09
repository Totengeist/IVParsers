<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
    /**
     * Normalize expectException function between PHPUnit 4 and PHPUnit 5.
     */
    public function customExpectException($class) {
        if (class_exists("\PHPUnit_Runner_Version") && version_compare(\PHPUnit_Runner_Version::id(), '4.0', '>=') && version_compare(\PHPUnit_Runner_Version::id(), '5.0', '<=')) {
            return parent::setExpectedException($class);
        }

        return parent::expectException($class);
    }
}
