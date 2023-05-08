<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Totengeist\IVParser\Section;

class SectionTest extends BaseTestCase {
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
        $section = new Section('Test', ['Key1 Value1', 'Key2 "Value 2"']);

        $this->assertEquals($section->content, ['Key1' => 'Value1', 'Key2' => 'Value 2']);
    }

    public function testAnEmptySectionValueIsIgnored() {
        $section = new Section('Test', ['Key1 Value1', 'Key2 "Value 2"', '      ']);

        $this->assertEquals($section->content, ['Key1' => 'Value1', 'Key2' => 'Value 2']);
    }

    public function testCanCreateSectionWithSections() {
        $section = new Section('Test', ['BEGIN Section1  Key1 Value1  Key2 "Value 2" END', 'BEGIN Section2  Key3 Value3  Key2 "Value 2" END']);

        $this->assertEquals(count($section->sections), 2);
        $this->assertEquals([
                                $section->sections['Section1']->content,
                                $section->sections['Section2']->content,
                            ], [
                                ['Key1' => 'Value1', 'Key2' => 'Value 2'],
                                ['Key3' => 'Value3', 'Key2' => 'Value 2'],
                            ]);
    }

    public function testCanAddEmptySubSection() {
        $section = new Section('', ['BEGIN Section1   END']);
        $this->assertTrue($section->section_exists('Section1'));
        $section = new Section('', ['BEGIN Section1 END']);
        $this->assertTrue($section->section_exists('Section1'));
    }

    public function testCanAddMultilineSubSection() {
        $section = new Section('', ['BEGIN Section1', '    Key1 Value1', '    Key2 Value 2', 'END']);
        $this->assertTrue($section->section_exists('Section1'));
    }

    public function testCanAddArraySubSection() {
        //$this->markTestIncomplete();
        $section = new Section('', ['BEGIN "[i 1]"    Key1 Value1    Key2 "Value 2"  END', 'BEGIN "[i 3]"    Key1 Value1    Key2 "Value 2"  END']);
        $this->assertEquals([
                                $section->sections[1]->content,
                                $section->sections[3]->content,
                            ], [
                                ['Key1' => 'Value1', 'Key2' => 'Value 2'],
                                ['Key1' => 'Value1', 'Key2' => 'Value 2'],
                            ]);
        $section = new Section('', ['BEGIN "[i 1]"', '    Key1 Value1', '    Key2 "Value 2"  ', 'END', 'BEGIN "[i 3]"', '    Key1 Value1', '    Key2 "Value 2"  ', 'END']);
        $this->assertEquals([
                                $section->sections[1]->content,
                                $section->sections[3]->content,
                            ], [
                                ['Key1' => 'Value1', 'Key2' => 'Value 2'],
                                ['Key1' => 'Value1', 'Key2' => 'Value 2'],
                            ]);
    }

    public function testCanAddSubfile() {
        $section = new Section('', ['Key1 Value1', 'BEGIN TestSection', '    Key1 Value1', '    Key2 Value 2', 'END'], 0, ['/TestSection' => TestSection::class]);
        $this->assertTrue($section->section_exists('TestSection'));
        $this->assertEquals(get_class($section->sections['TestSection']), "Tests\TestSection");
    }
}

class TestSection extends Section {
    public function __construct($structure = null, $level = 0, $subfiles = []) {
        parent::__construct('', $structure, $level, $subfiles);
    }
}
