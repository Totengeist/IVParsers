<?php
/**
 * Classes necessary for processing a definitions.txt file.
 */

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\IVFile;
use Totengeist\IVParser\Section;

/**
 * Classes necessary for processing a `.ship` file.
 */
class DefinitionsFile extends IVFile {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $REQUIRED_SECTIONS = array('Definition');

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid ship file before calling the standard constructor.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array('/Definition' => 'Totengeist\IVParser\TheLastStarship\Definition')) {
        parent::__construct($structure, $level, $subfiles);
        if (!$this->is_valid() && $structure !== array()) {
            throw new InvalidFileException('definitions');
        }
    }

    /**
     * Get a specific definition.
     *
     * @param string $type the definition type to get
     *
     * @return Definition|null the definition
     */
    public function get_definition($type) {
        foreach ($this->get_definitions() as $definition) {
            if ($definition->content['Type'] == $type) {
                return $definition;
            }
        }

        return null;
    }

    /**
     * Get all definitions.
     *
     * @return Definition[] the definitions
     */
    public function get_definitions() {
        /** @var Definition[] $definitions */
        $definitions = $this->get_repeatable_section('Definition');

        return $definitions;
    }
}

/**
 * A definitions.txt definition.
 */
class Definition extends Section {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $REQUIRED_SECTIONS = array('Equipment');

    /**
     * An intermediary constructor.
     *
     * @param string[] $structure the structure of the section and its subsections
     * @param int      $level     the indentation level of the section in the original file
     * @param string[] $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct('', $structure, $level, $subfiles);
    }

    /**
     * Get equipment metadata.
     *
     * @return string[] the metadata
     */
    public function get_metadata() {
        return $this->get_unique_section('Equipment')->content;
    }

    /**
     * Get equipment ports.
     *
     * @return Section[] the ports
     */
    public function get_ports() {
        return $this->get_repeatable_section('Port');
    }

    /**
     * Get equipment slots.
     *
     * @return Section[] the slots
     */
    public function get_slots() {
        return $this->get_repeatable_section('Slot');
    }

    /**
     * Get equipment placement rules.
     *
     * @return Section[] the placement rules
     */
    public function get_placement_rules() {
        return $this->get_repeatable_section('PlacementRule');
    }
}
