<?php

/**
 * Classes necessary for processing a `.space` file.
 */

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\IVFile;
use Totengeist\IVParser\Section;

class SaveFile extends IVFile {
    protected static $requiredSections = array('Galaxy');
    protected static $fileType = 'application/tls-save+introversion';

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid save file before calling the standard constructor.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array('/Layer' => 'Totengeist\IVParser\TheLastStarship\ShipFile')) {
        parent::__construct($structure, $level, $subfiles);
        if (!$this->isValid() && $structure !== array()) {
            throw new InvalidFileException('save');
        }
    }

    /**
     * Retrieve the game mode of the save file.
     *
     * @return string
     */
    public function getGameMode() {
        if (isset($this->content['GameMode'])) {
            return $this->content['GameMode'];
        }

        return 'Survival';
    }

    /**
     * Set the game mode of the save file.
     *
     * @param string $mode the game mode to set
     *
     * @return void
     */
    public function setGameMode($mode) {
        $this->content['GameMode'] = $mode;
    }

    /**
     * Check whether the save supports multi-system simulation.
     *
     * @return bool
     */
    public function hasMultiSystemSimulation() {
        if (isset($this->content['MultiSystemSimulation'])) {
            return 'true' == strtolower($this->content['MultiSystemSimulation']);
        }

        return false;
    }

    /**
     * Enable multi-system simulation.
     *
     * @return void
     */
    public function enableMultiSystemSimulation() {
        $this->content['MultiSystemSimulation'] = 'true';
    }

    /**
     * Disable multi-system simulation.
     *
     * @return void
     */
    public function disableMultiSystemSimulation() {
        $this->content['MultiSystemSimulation'] = 'false';
    }

    /**
     * Retrieve ships stored in the save file.
     *
     * @return ShipFile[] the ship files
     */
    public function getLayers() {
        if ($this->sectionExists('Layer')) {
            /** @var ShipFile[] $layers */
            $layers = $this->getRepeatableSection('Layer');

            return $layers;
        }

        return array();
    }

    /**
     * Retrieve ships stored in the save file by categorization.
     *
     * @param string $type the type of ships to retrieve
     *
     * @return ShipFile[] the ship files
     */
    public function getShips($type = null) {
        if ($type == null) {
            return $this->getLayers();
        }
        $ships = array();
        foreach ($this->getLayers() as $ship) {
            if ($ship->content['Type'] == $type) {
                $ships[] = $ship;
            }
        }

        return $ships;
    }

    /**
     * Retrieve missions from all ships stored in the save file.
     *
     * @return Section[] the mission information
     */
    public function getMissions() {
        if (!$this->sectionExists('Missions/Missions')) {
            return array();
        }
        $missions = array();
        $section = $this->getUniqueSection('Missions/Missions');
        if ($section == null) {
            return array();
        }
        foreach ($section->sections as $mission) {
            if (is_array($mission)) {
                $mission = $mission[count($mission)-1];
            }
            $missions[] = $mission;
        }

        return $missions;
    }

    /**
     * Retrieve basic information about the galaxy.
     *
     * @return int[] the galaxy information
     */
    public function getGalaxyInfo() {
        $info = array();
        $galaxy = $this->getUniqueSection('Galaxy')->content;
        $info['SectorCount'] = isset($galaxy['SectorCount']) ? intval($galaxy['SectorCount']) : 0;
        $info['CurrentSystem'] = intval($galaxy['CurrentSystem']);
        $info['EntrySystem'] = isset($galaxy['EntrySystem']) ? intval($galaxy['EntrySystem']) : -1;
        $info['ExitSystem'] = isset($galaxy['ExitSystem']) ? intval($galaxy['ExitSystem']) : -1;

        return $info;
    }

    /**
     * Get the save file's version number.
     *
     * Save files during the playtest did not have version number, so we return 0 if no version is
     * found.
     *
     * @return int the version of the save file
     */
    public function getSaveVersion() {
        if (isset($this->content['SaveVersion'])) {
            return intval($this->content['SaveVersion']);
        }

        return 0;
    }
}
