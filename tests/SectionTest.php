<?php

namespace Tests;

use Totengeist\IVParser\Section;

class SectionTest extends TestCase {
    public function testCanCreateEmptySection() {
        $section = new Section();

        $this->assertEquals(get_class($section), "Totengeist\IVParser\Section");
    }

    public function testCanAddSection() {
        $section = new Section();
        $section->add_section('Section1', new Section('Section1'));

        $this->assertEquals(count($section->sections), 1);
        $this->assertTrue(isset($section->sections['Section1']));
    }

    public function testCanCheckSectionExists() {
        $section = new Section();
        $this->assertFalse($section->section_exists('Section1'));

        $section->add_section('Section1', new Section('Section1'));
        $this->assertTrue($section->section_exists('Section1'));
    }

    public function testCanAddSectionTwice() {
        $section = new Section();
        $section->add_section('Section1', new Section('Section1'));
        $section->add_section('Section1', new Section('Section1'));
        $section->add_section('Section1', new Section('Section1'));

        $this->assertEquals(count($section->sections), 1);
        $this->assertEquals(count($section->sections['Section1']), 3);
        $this->assertTrue(is_array($section->sections['Section1']));
    }

    public function testCanCreateSectionWithContent() {
        $section = new Section('Test', array('Key1 Value1', 'Key2 "Value 2"'));

        $this->assertEquals($section->content, array('Key1' => 'Value1', 'Key2' => 'Value 2'));
    }

    public function testAnEmptySectionValueIsIgnored() {
        $section = new Section('Test', array('Key1 Value1', 'Key2 "Value 2"', '      '));

        $this->assertEquals($section->content, array('Key1' => 'Value1', 'Key2' => 'Value 2'));
    }

    public function testCanCreateSectionWithSections() {
        $section = new Section('Test', array('BEGIN Section1  Key1 Value1  Key2 "Value 2" END', 'BEGIN Section2  Key3 Value3  Key2 "Value 2" END'));

        $this->assertEquals(count($section->sections), 2);
        $this->assertEquals(array(
                                $section->sections['Section1']->content,
                                $section->sections['Section2']->content,
                            ), array(
                                array('Key1' => 'Value1', 'Key2' => 'Value 2'),
                                array('Key3' => 'Value3', 'Key2' => 'Value 2'),
                            ));
    }

    public function testCanAddEmptySubSection() {
        $section = new Section('', array('BEGIN Section1   END'));
        $this->assertTrue($section->section_exists('Section1'));
        $section = new Section('', array('BEGIN Section1 END'));
        $this->assertTrue($section->section_exists('Section1'));
    }

    public function testCanAddMultilineSubSection() {
        $section = new Section('', array('BEGIN Section1', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $this->assertTrue($section->section_exists('Section1'));
    }

    public function testCanAddArraySubSection() {
        $section = new Section('', array('BEGIN "[i 1]"    Key1 Value1    Key2 "Value 2"  END', 'BEGIN "[i 3]"    Key1 Value1    Key2 "Value 2"  END'));
        $this->assertEquals(array(
                                $section->sections[1]->content,
                                $section->sections[3]->content,
                            ), array(
                                array('Key1' => 'Value1', 'Key2' => 'Value 2'),
                                array('Key1' => 'Value1', 'Key2' => 'Value 2'),
                            ));
        $section = new Section('', array('BEGIN "[i 1]"', '    Key1 Value1', '    Key2 "Value 2"  ', 'END', 'BEGIN "[i 3]"', '    Key1 Value1', '    Key2 "Value 2"  ', 'END'));
        $this->assertEquals(array(
                                $section->sections[1]->content,
                                $section->sections[3]->content,
                            ), array(
                                array('Key1' => 'Value1', 'Key2' => 'Value 2'),
                                array('Key1' => 'Value1', 'Key2' => 'Value 2'),
                            ));
    }

    public function testCanAddSubfile() {
        $section = new Section('', array('Key1 Value1', 'BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'), 0, array('/TestSection' => "Tests\TestSection"));
        $this->assertTrue($section->section_exists('TestSection'));
        $this->assertEquals(get_class($section->sections['TestSection']), "Tests\TestSection");
    }

    public function testCanGetUniqueSection() {
        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $section = $section->get_unique_section('TestSection');
        $this->assertEquals($section->content['Key1'], 'Value1');

        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END', 'BEGIN TestSection', '    Key1 Value3', '    Key2 Value 4', 'END'));
        $section = $section->get_unique_section('TestSection');
        $this->assertEquals($section->content['Key1'], 'Value3');

        $this->customExpectException('Totengeist\IVParser\Exception\SectionNotFoundException');
        $section = new Section('', array(), 0, array('/TestSection' => "Tests\TestSection"));
        $section->get_unique_section('TestSection');
    }

    public function testCanGetRepeatableSection() {
        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'));
        $section = $section->get_repeatable_section('TestSection');
        $this->assertEquals($section[0]->content['Key1'], 'Value1');

        $section = new Section('', array('BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END', 'BEGIN TestSection', '    Key1 Value3', '    Key2 Value 4', 'END'));
        $section = $section->get_repeatable_section('TestSection');
        $this->assertEquals($section[1]->content['Key1'], 'Value3');

        $this->customExpectException('Totengeist\IVParser\Exception\SectionNotFoundException');
        $section = new Section('', array(), 0, array('/TestSection' => "Tests\TestSection"));
        $section->get_repeatable_section('TestSection');
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
        $this->assertEquals((string) $section, "\n" .
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
            "BEGIN TestSection2         END\n"
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
