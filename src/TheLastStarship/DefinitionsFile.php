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
    protected static $requiredSections = array('Definition');
    protected static $fileType = 'application/tls-definitions+introversion';

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
        if (!$this->isValid() && $structure !== array()) {
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
    public function getDefinition($type) {
        foreach ($this->getDefinitions() as $definition) {
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
    public function getDefinitions() {
        /** @var Definition[] $definitions */
        $definitions = $this->getRepeatableSection('Definition');

        return $definitions;
    }
}

/**
 * A definitions.txt definition.
 */
class Definition extends Section {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $requiredSections = array('Equipment');

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
    public function getMetadata() {
        return $this->getUniqueSection('Equipment')->content;
    }

    /**
     * Get equipment ports.
     *
     * @return Section[] the ports
     */
    public function getPorts() {
        return $this->getRepeatableSection('Port');
    }

    /**
     * Get equipment slots.
     *
     * @return Section[] the slots
     */
    public function getSlots() {
        return $this->getRepeatableSection('Slot');
    }

    /**
     * Get equipment placement rules.
     *
     * @return Section[] the placement rules
     */
    public function getPlacementRules() {
        return $this->getRepeatableSection('PlacementRule');
    }
}
