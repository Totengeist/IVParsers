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

    /**
     * Debug print function that should be replaced with something more useful.
     *
     * @return void
     */
    public function printInfo() {
        if ($this->sectionExists('Layer')) {
            $all_ships = count($this->getLayers());
            $fleet = $this->getShips('FriendlyShip');
            $fleet_cnt = count($fleet);
            $hostiles = count($this->getShips('HostileShip'));
            $ships = array();
            foreach ($fleet as $ship) {
                $ships[] = $ship->content['Name'];
            }
        } else {
            $all_ships = 0;
            $fleet_cnt = 0;
            $hostiles = 0;
            $ships = array();
        }
        $galaxy = $this->getGalaxyInfo();

        $template = 'Your verison %d save file has %2d active missions, %2d ships in your fleet. The current sector is %2d. The current system (%3d) has %2d neutral and %2d hostile ships. Your fleet contains: %s.';
        echo sprintf($template,
            $this->getSaveVersion(),
            count($this->getMissions()),
            $fleet_cnt,
            $galaxy['SectorCount'],
            $galaxy['CurrentSystem'],
            $all_ships-$fleet_cnt-$hostiles,
            $hostiles,
            implode(', ', $ships)
        );
    }
}
