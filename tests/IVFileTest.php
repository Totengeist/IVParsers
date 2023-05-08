<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Totengeist\IVParser\IVFile;

class IVFileTest extends BaseTestCase {
    public function testCanCreateEmptyIVFile() {
        $file = new IVFile();

        $this->assertEquals(get_class($file), "Totengeist\IVParser\IVFile");
    }

    public function testCanCreateFullIVFile() {
        $file = new IVFile('');

        $this->assertEquals(get_class($file), "Totengeist\IVParser\IVFile");
    }
}
