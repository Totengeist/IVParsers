<?php

namespace Totengeist\IVParser;

use Totengeist\IVParser\Exception\InvalidFileException;

/**
 * Classes necessary for processing a `.space` file.
 *
 * A save file for The Last Starship can contain subfiles such as:
 *  * ships
 */
class SaveFile extends IVFile {
    protected static $REQUIRED_SECTIONS = array('Galaxy');

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid save file before calling the standard constructor.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array('/Layer' => 'Totengeist\IVParser\ShipFile')) {
        parent::__construct($structure, $level, $subfiles);
        if (!$this->is_valid() && $structure !== array()) {
            throw new InvalidFileException('save');
        }
    }

    /**
     * Retrieve ships stored in the save file.
     *
     * @return Section[] the ship files
     */
    public function get_layers() {
        if ($this->section_exists('Layer')) {
            return $this->get_repeatable_section('Layer');
        }

        return array();
    }

    /**
     * Retrieve ships stored in the save file by categorization.
     *
     * @param string $type the type of ships to retrieve
     *
     * @return Section[]|array<string, Section[]> the ship files
     */
    public function get_ships($type = null) {
        $ships = array();
        $layers = $this->get_layers();

        foreach ($layers as $ship) {
            $ships[$ship->content['Type']][] = $ship;
        }

        if ($type !== null) {
            if (isset($ships[$type])) {
                return $ships[$type];
            }

            return array();
        }

        return $ships;
    }

    /**
     * Retrieve missions from all ships stored in the save file.
     *
     * @return Section[] the mission information
     */
    public function get_missions() {
        if (!$this->section_exists('Missions/Missions')) {
            return array();
        }
        $missions = array();
        $section = $this->get_unique_section('Missions/Missions');
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
    public function get_galaxy_info() {
        $info = array();
        $galaxy = $this->get_unique_section('Galaxy')->content;
        $info['SectorCount'] = isset($galaxy['SectorCount']) ? intval($galaxy['SectorCount']) : 0;
        $info['CurrentSystem'] = intval($galaxy['CurrentSystem']);
        $info['EntrySystem'] = intval($galaxy['EntrySystem']);
        $info['ExitSystem'] = intval($galaxy['ExitSystem']);

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
    public function get_save_version() {
        if (isset($this->content['SaveVersion'])) {
            return intval($this->content['SaveVersion']);
        }

        return 0;
    }

    /**
     * Debug print fucntion that should be replaced with something more useful.
     *
     * @return void
     */
    public function print_info() {
        if ($this->section_exists('Layer')) {
            $all_ships = count($this->get_layers());
            $fleet = $this->get_ships('FriendlyShip');
            $fleet_cnt = count($fleet);
            $hostiles = count($this->get_ships('HostileShip'));
            $ships = array();
            foreach ($fleet as $ship) {
                if (is_array($ship)) {
                    $ship = $ship[count($ship)-1];
                }
                $ships[] = $ship->content['Name'];
            }
        } else {
            $all_ships = 0;
            $fleet_cnt = 0;
            $hostiles = 0;
            $ships = array();
        }
        $galaxy = $this->get_galaxy_info();

        $template = 'Your verison %d save file has %2d active missions, %2d ships in your fleet. The current sector is %2d. The current system (%3d) has %2d neutral and %2d hostile ships. Your fleet contains: %s.';
        echo sprintf($template,
            $this->get_save_version(),
            count($this->get_missions()),
            $fleet_cnt,
            $galaxy['SectorCount'],
            $galaxy['CurrentSystem'],
            $all_ships-$fleet_cnt-$hostiles,
            $hostiles,
            implode(', ', $ships)
        );
    }
}
