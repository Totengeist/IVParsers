<?php

namespace Tests;

use Totengeist\IVParser\CompatibilityMatrix;

class CompatibilityTest extends TestCase {
    public function testCanDiff() {
        $matrix = new CompatibilityMatrix();

        $this->assertEquals(array('test1', 'test2', 'test3'), $matrix->updateCategory(array('+' => array('test3')), array('test1', 'test2')));
        $this->assertEquals(array('test1', 'test2', 'test3'), $matrix->updateCategory(array('-' => array('test4')), array('test1', 'test2', 'test3', 'test4')));
        $this->assertEquals(array('test1', 'test2', 'test3'), $matrix->updateCategory(array('+' => array('test4'), '-' => array('test4')), array('test1', 'test2', 'test3')));
        $this->assertEquals(array('test1', 'test2', 'test3', 'test4'), $matrix->updateCategory(array('+' => array('test4'), '-' => array('test4')), array('test1', 'test2', 'test3', 'test4')));
        $this->assertEquals(array('test4', 'test5', 'test6'), $matrix->updateCategory(array('_' => array('test4', 'test5', 'test6')), array('test1', 'test2', 'test3')));
        $this->assertEquals(array('test4', 'test5', 'test6'), $matrix->updateCategory(array('_' => array('test4', 'test5', 'test6'), '+' => array('test7'), '-' => array('test5')), array('test1', 'test2', 'test3')));
    }

    public function testCanLoadMatrix() {
        $json = '
{
    "versions": {
        "version1": {
            "option1": {
                "_": ["item1", "item2"]
            }
        },
        "version2": {
            "option1": {
                "+": ["item3", "item4", "item4"],
                "-": ["item1"]
            },
            "option2": {
                "+": ["item1"]
            }
        },
        "version3": {
            "option1": {
                "+": ["item3"],
                "-": ["item3"]
            },
            "option2": {
                "-": ["item1"]
            }
        }
    }
}
';
        $expected = array(
            'version1' => array(
                'option1' => array('item1', 'item2')
            ),
            'version2' => array(
                'option1' => array('item2', 'item3', 'item4'),
                'option2' => array('item1')
            ),
            'version3' => array(
                'option1' => array('item2', 'item3', 'item4'),
                'option2' => array()
            )
        );

        $matrix = new CompatibilityMatrix();
        $matrix->buildCompatibilityMatrix($json);
        $this->assertEquals($expected, $matrix->matrix);
    }

    public function testCannotLoadMatrixWithoutVersions() {
        $this->customExpectException('Totengeist\IVParser\Exception\InvalidFileException');
        $json = '
{
    "blahblah": {}
}
';
        $matrix = new CompatibilityMatrix();
        $matrix->buildCompatibilityMatrix($json);
    }
}
