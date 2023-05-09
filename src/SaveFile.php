<?php

namespace Totengeist\IVParser;

/**
 * Classes necessary for processing a `.space` file.
 *
 * A save file for The Last Starship can contain subfiles such as:
 *  * ships
 */
class SaveFile extends IVFile {
    public $info = array();

    /**
     * The types of ships available in the game.
     *
     *  * FriendlyShip - a player-controlled ship
     *  * HostileShip - an enemy ship, which will attack player-controlled and neutral ships
     *  * NeutralShip - an NPC ship controlled by AI
     *  * ShipForSale - a ship hull available to a shipyard that can be purchased by the player
     *  * Derelict - a stranded ship that can be looted
     */
    const SHIPS = array('FriendlyShip', 'HostileShip', 'ShipForSale', 'NeutralShip', 'Derelict');

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid save file before calling the standard constructor.
     *
     * @param array $structure the structure of the section and its subsections
     * @param int   $level     the indentation level of the section in the original file
     * @param array $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = null, $level = 0, $subfiles = array('/Layer' => 'Totengeist\IVParser\ShipFile')) {
        if (!$this->is_save($structure, $level)) {
            throw new \Exception('This is not a save file.');
        }
        parent::__construct($structure, $level, $subfiles);
    }

    /**
     * Verify the given structure is a save file.
     *
     * We check for the Galaxy section as a unique section to save files.
     *
     * @param array $structure the structure of the section and its subsections
     * @param int   $level     the indentation level of the section in the original file
     *
     * @return bool is it a valid save file?
     */
    public function is_save($structure, $level) {
        if (is_string($structure)) {
            $structure = preg_split('/\r?\n/', $structure);
        }
        foreach ($structure as $line) {
            if (strpos($line, str_repeat('    ', $level) . 'BEGIN Galaxy') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve ships stored in the save file.
     *
     * @return array the ship files
     */
    public function get_layers() {
        if ($this->section_exists('Layer')) {
            $section = $this->get_section('Layer');
            if (is_array($section)) {
                return $section;
            }

            return array($section);
        }

        return array();
    }

    /**
     * Retrieve ships stored in the save file by categorization.
     *
     * @return array the ship files
     */
    public function get_ships($type = null) {
        $ships = array();
        $layers = $this->get_layers();

        foreach ($layers as $ship) {
            $ships[$ship->info['Type']][] = $ship;
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
     * @return array the mission information
     */
    public function get_missions() {
        if ($this->section_exists('Layer')) {
            $missions = array();
            $section = $this->get_section('Missions/Missions');
            if ($section == null) {
                return array();
            }
            foreach ($section->sections as $mission) {
                $missions[] = $mission;
            }

            return $missions;
        }

        return array();
    }

    /**
     * Retrieve basic information about the galaxy.
     *
     * @return array the galaxy information
     */
    public function get_galaxy_info() {
        $info = array();
        $galaxy = $this->get_section('Galaxy')->content;
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
     */
    public function print_info() {
        if ($this->section_exists('Layer')) {
            $all_ships = count($this->get_layers());
            $fleet = $this->get_ships('FriendlyShip');
            $fleet_cnt = count($fleet);
            $hostiles = count($this->get_ships('HostileShip'));
            $ships = array();
            foreach ($fleet as $ship) {
                $ships[] = $ship->info['Name'];
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
