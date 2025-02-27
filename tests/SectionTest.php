<?php

namespace Tests;

use Totengeist\IVParser\Section;

class SectionTest extends TestCase {
    public function testCanCreateEmptySection() {
        $section = new Section();

        $this->assertEquals("Totengeist\IVParser\Section", get_class($section));
    }

    public function testCanAddSection() {
        $section = new Section();
        $section->addSection('Section1', new Section('Section1'));

        $this->assertEquals(1, count($section->sections));
        $this->assertTrue(isset($section->sections['Section1']));
    }

    public function testCanCheckSectionExists() {
        $section = new Section();
        $this->assertFalse($section->sectionExists('Section1'));

        $section->addSection('Section1', new Section('Section1'));
        $this->assertTrue($section->sectionExists('Section1'));
    }

    public function testCanAddSectionTwice() {
        $section = new Section();
        $section->addSection('Section1', new Section('Section1'));
        $section->addSection('Section1', new Section('Section1'));
        $section->addSection('Section1', new Section('Section1'));

        $this->assertEquals(1, count($section->sections));
        $this->assertEquals(3, count($section->sections['Section1']));
        $this->assertTrue(is_array($section->sections['Section1']));
    }

    public function testCanCreateSectionWithContent() {
        $section = new Section('Test', array('Key1 Value1', 'Key2 "Value 2"'));

        $this->assertEquals(array('Key1' => 'Value1', 'Key2' => 'Value 2'), $section->content);
    }

    public function testAnEmptySectionValueIsIgnored() {
        $section = new Section('Test', array('Key1 Value1', 'Key2 "Value 2"', '      '));

        $this->assertEquals(array('Key1' => 'Value1', 'Key2' => 'Value 2'), $section->content);
    }

    public function testCanCreateSectionWithSections() {
        $section = new Section('Test', array('BEGIN Section1  Key1 Value1  Key2 "Value 2" END', 'BEGIN Section2  Key3 Value3  Key2 "Value 2" END'));

        $this->assertEquals(2, count($section->sections));
        $this->assertEquals(array(
            array('Key1' => 'Value1', 'Key2' => 'Value 2'),
            array('Key3' => 'Value3', 'Key2' => 'Value 2'),
        ), array(
            $section->sections['Section1']->content,
            $section->sections['Section2']->content,
        ));
    }

    public function testCanAddEmptySubSection() {
        $section = new Section('', array('BEGIN Section1   END'));
        $this->assertTrue($section->sectionExists('Section1'));
        $section = new Section('', array('BEGIN Section1 END'));
        $this->assertTrue($section->sectionExists('Section1'));
    }

    public function testCanAddMultilineSubSection() {
        $section = new Section('', array('BEGIN Section1', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $this->assertTrue($section->sectionExists('Section1'));
    }

    public function testCanAddArraySubSection() {
        $section = new Section('', array('BEGIN "[i 1]"    Key1 Value1    Key2 "Value 2"  END', 'BEGIN "[i 3]"    Key1 Value1    Key2 "Value 2"  END'));
        $this->assertEquals(array(
            array('Key1' => 'Value1', 'Key2' => 'Value 2'),
            array('Key1' => 'Value1', 'Key2' => 'Value 2'),
        ), array(
            $section->sections[1]->content,
            $section->sections[3]->content,
        ));
        $section = new Section('', array('BEGIN "[i 1]"', '    Key1 Value1', '    Key2 "Value 2"  ', 'END', 'BEGIN "[i 3]"', '    Key1 Value1', '    Key2 "Value 2"  ', 'END'));
        $this->assertEquals(array(
            array('Key1' => 'Value1', 'Key2' => 'Value 2'),
            array('Key1' => 'Value1', 'Key2' => 'Value 2'),
        ), array(
            $section->sections[1]->content,
            $section->sections[3]->content,
        ));
    }

    public function testCanAddSubfile() {
        $section = new Section('', array('Key1 Value1', 'BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'), 0, array('/TestSection' => "Tests\TestSection"));
        $this->assertTrue($section->sectionExists('TestSection'));
        $this->assertEquals("Tests\TestSection", get_class($section->sections['TestSection']));
    }

    public function testCanGetUniqueSection() {
        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $section = $section->getUniqueSection('TestSection');
        $this->assertEquals('Value1', $section->content['Key1']);

        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END', 'BEGIN TestSection', '    Key1 Value3', '    Key2 Value 4', 'END'));
        $section = $section->getUniqueSection('TestSection');
        $this->assertEquals('Value3', $section->content['Key1']);

        $this->customExpectException('Totengeist\IVParser\Exception\SectionNotFoundException');
        $section = new Section('', array(), 0, array('/TestSection' => "Tests\TestSection"));
        $section->getUniqueSection('TestSection');
    }

    public function testCanGetRepeatableSection() {
        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $section = $section->getRepeatableSection('TestSection');
        $this->assertEquals('Value1', $section[0]->content['Key1']);

        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END', 'BEGIN TestSection', '    Key1 Value3', '    Key2 Value 4', 'END'));
        $section = $section->getRepeatableSection('TestSection');
        $this->assertEquals('Value3', $section[1]->content['Key1']);

        $this->customExpectException('Totengeist\IVParser\Exception\SectionNotFoundException');
        $section = new Section('', array(), 0, array('/TestSection' => "Tests\TestSection"));
        $section->getRepeatableSection('TestSection');
    }

    public function testCanConvertSectionToString() {
        $section = new Section('', array(
            'TestKey1  Testing',
            'TestKey2  "Testing with spaces"',
            'BEGIN TestSection1',
            '    TestKey1  Testing',
            'END',
            'BEGIN TestSection2',
            '    BEGIN "[i 1]"    Key1 Value1    Key2 "Value 2"  END',
            '    BEGIN "[i 3]"',
            '        Key1HasAReallyLongName Value1',
            '        Key2 "Value 2"',
            '        BEGIN TestSubsection1 END',
            '    END',
            '    BEGIN "[i 4]"  END',
            'END',
            'BEGIN TestSection2  END',
        ));
        $this->assertEquals("\n" .
            "TestKey1             Testing  \n" .
            "TestKey2             \"Testing with spaces\"  \n" .
            "BEGIN TestSection1         TestKey1 Testing  END\n" .
            "BEGIN TestSection2         \n" .
            "    BEGIN \"[i 1]\"      Key1 Value1  Key2 \"Value 2\"  END\n" .
            "    BEGIN \"[i 3]\"      \n" .
            "        Key1HasAReallyLongName         Value1  \n" .
            "        Key2                           \"Value 2\"  \n" .
            "        BEGIN TestSubsection1      END\n" .
            "    END\n" .
            "    BEGIN \"[i 4]\"      END\n" .
            "END\n" .
            "BEGIN TestSection2         END\n", (string) $section
        );
    }
}

class TestSection extends Section {
    /**
     * @param string[] $structure the structure of the section and its subsections
     * @param int      $level     the indentation level of the section in the original file
     * @param string[] $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct('', $structure, $level, $subfiles);
    }
}
