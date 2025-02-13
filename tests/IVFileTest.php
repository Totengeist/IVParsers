<?php

namespace Tests;

use Totengeist\IVParser\IVFile;

class IVFileTest extends TestCase {
    public function testCanCreateEmptyIVFile() {
        $file = new IVFile();

        $this->assertEquals(get_class($file), "Totengeist\IVParser\IVFile");
    }

    public function testCanCreateFullIVFile() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $file = new IVFile('
Id                   0  
Name                 Empty  
Type                 FriendlyShip  
BEGIN Objects    
    Size                 1  
    BEGIN "[i 0]"      
        Id                   9  
        Type                 DockingPort  
        Width                4  
        Height               2  
        Orientation          Down  
        BEGIN Slots      
            Size                 4  
            BEGIN "[i 0]"      X 3  END
            BEGIN "[i 1]"      X 2  END
            BEGIN "[i 2]"      X 1  END
            BEGIN "[i 3]"      END
        END
    END
END
BEGIN WorkQueue  Workers "[2, 3, 4, 5, 6]"  END
BEGIN Welfare    Timer 47.940788269042969  END
BEGIN Habitation SewageTimer 0.068042416666656891  END
BEGIN Deliveries 
    Timer                0.055459458380937576  
    IdleTime             5.0047349929809570  
    BEGIN Trade      END
END
');

        $this->assertEquals(get_class($file), "Totengeist\IVParser\IVFile");
        $this->assertTrue($file->section_exists('Deliveries'));
        $this->assertEquals($file->get_section('Deliveries')->content['Timer'], '0.055459458380937576');
    }

    public function testStructureIsValid() {
        $section = new TestIVFile(array('Key1 Value1', 'BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $this->assertTrue($section->is_valid());

        $section = new TestIVFile(array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $this->assertFalse($section->is_valid());

        $section = new TestIVFile(array('Key1 Value1'));
        $this->assertFalse($section->is_valid());
    }
}

class TestIVFile extends IVFile {
    protected static $REQUIRED_CONTENT = array('Key1');
    protected static $REQUIRED_SECTIONS = array('TestSection');

    /**
     * @param string[] $structure the structure of the section and its subsections
     * @param int      $level     the indentation level of the section in the original file
     * @param string[] $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct($structure, $level, $subfiles);
    }
}
