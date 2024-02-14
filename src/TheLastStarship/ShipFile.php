<?php

/**
 * Classes necessary for processing a `.ship` file.
 */

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\IVFile;

class ShipFile extends IVFile {
    protected static $REQUIRED_SECTIONS = array('Habitation');
    protected static $FILE_TYPE = 'application/tls-ship+introversion';
    /**
     * The types of ships available in the game.
     *
     *  * FriendlyShip - a player-controlled ship
     *  * HostileShip - an enemy ship, which will attack player-controlled and neutral ships
     *  * NeutralShip - an NPC ship controlled by AI
     *  * ShipForSale - a ship hull available to a shipyard that can be purchased by the player
     *  * Derelict - a stranded ship that can be looted
     *
     * @var string[]
     */
    public static $SHIPS = array('FriendlyShip', 'HostileShip', 'ShipForSale', 'NeutralShip', 'Derelict');

    /** @var string[] */
    public static $WEAPONS = array('GatlingGun', 'Cannon', 'Railgun');
    /** @var string[] */
    public static $ENGINES = array('Engine');
    /** @var string[] */
    public static $POWER = array('Reactor', 'FusionReactor');
    /** @var string[] */
    public static $LOGISTICS = array('MiningLaser', 'DroneBay');
    /** @var string[] */
    public static $THRUSTERS = array('Thruster');
    /** @var string[] */
    public static $CELLS = array('Hull', 'Interior', 'Floor', 'Habitation', 'Armour');
    /** @var string[] */
    public static $CELL_TYPES = array('Storage');
    /** @var string[] */
    public static $TANKS = array('TinyTank', 'SmallTank', 'Tank');
    /** @var string[] */
    public static $RESOURCES = array('Fuel', 'Oxygen', 'Water', 'Sewage', 'WasteWater', 'CarbonDioxide', 'Deuterium');

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid ship file before calling the standard constructor.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct($structure, $level, $subfiles);
        if (!$this->is_valid() && $structure !== array()) {
            throw new InvalidFileException('ship');
        }
    }

    /**
     * Get the overall tank capacity for each type of resource.
     *
     * @return float[] an associative array of resource capacities
     */
    public function get_tank_capacity_by_type() {
        $tanks = array();

        foreach (static::$RESOURCES as $resource) {
            $tanks[$resource] = 0.0;
        }

        foreach ($this->get_tanks() as $tank_type) {
            foreach ($tank_type as $tank) {
                if (isset($tank['Resource']) && isset($tank['Capacity'])) {
                    $tanks[$tank['Resource']] += (float) $tank['Capacity'];
                }
            }
        }

        return $tanks;
    }

    /**
     * Get the count of generators and their overall output.
     *
     * @return array{float, array<string, int>}
     */
    public function get_generator_count_and_output() {
        $output = 0;
        $count = array();
        foreach ($this->get_generators() as $type => $generators) {
            $count[$type] = count($generators);
            foreach ($generators as $power) {
                $output += (float) $power['PowerOutput'];
            }
        }

        return array($output, $count);
    }

    /**
     * Get items by type given an associative array of types.
     *
     * @param string[]|string $type a list of the objects to retrieve
     *
     * @return array<string, array<string[]>> the items, grouped by type
     */
    public function get_items_by_type($type) {
        if (is_string($type)) {
            $type = array($type);
        }
        $items = array();

        foreach ($type as $item) {
            $results = $this->get_object_content($item);
            if ($results !== array()) {
                $items[$item] = $results;
            }
        }

        return $items;
    }

    /**
     * Get weapons by type.
     *
     * @return array<string, array<string[]>> weapons, grouped by type
     */
    public function get_weapons() {
        return $this->get_items_by_type(static::$WEAPONS);
    }

    /**
     * Get engines by type.
     *
     * @return array<string, array<string[]>> engines, grouped by type
     */
    public function get_engines() {
        return $this->get_items_by_type(static::$ENGINES);
    }

    /**
     * Get logistics equipment by type.
     *
     * @return array<string, array<string[]>> logistics equipment, grouped by type
     */
    public function get_logistics() {
        return $this->get_items_by_type(static::$LOGISTICS);
    }

    /**
     * Get generators by type.
     *
     * @return array<string, array<string[]>> generators, grouped by type
     */
    public function get_generators() {
        return $this->get_items_by_type(static::$POWER);
    }

    /**
     * Get thrusters by type.
     *
     * @return array<string, array<string[]>> thrusters, grouped by type
     */
    public function get_thrusters() {
        return $this->get_items_by_type(static::$THRUSTERS);
    }

    /**
     * Get tanks by type.
     *
     * @return array<string, array<string[]>> tanks, grouped by type
     */
    public function get_tanks() {
        return $this->get_items_by_type(static::$TANKS);
    }

    /**
     * Get the counts of items by type given an associative array of types.
     *
     * @param string[] $type a list of the objects to retrieve
     *
     * @return int[] the item counts, grouped by type
     */
    public function get_item_counts_by_type($type) {
        $counts = array();

        foreach ($type as $key) {
            $counts[$key] = 0;
        }

        foreach ($this->get_items_by_type($type) as $key => $content) {
            $counts[$key] = count($content);
        }

        return $counts;
    }

    /**
     * Get the number of cells per type.
     *
     * Combine the GridMap/Palette and GridMap/Cells regions into one array containing the relevant
     * information.
     *
     * @return int[] the cell counts by type
     */
    public function get_cell_info() {
        $types = array();
        $cells = array();
        foreach ($this->get_unique_section('GridMap/Palette')->sections as $cell) {
            if (is_array($cell)) {
                $cell = end($cell);
            }
            /** @var \Totengeist\IVParser\Section $cell */
            $path = explode('/', $cell->path);
            $name = $path[count($path)-1];
            // If the name is empty, the character is '/' and was exploded.
            if ($name == '') {
                $name = '/';
            }
            // If the cell is empty space, it contains no content.
            if (count($cell->content) == 0) {
                $types[$name] = array();
            } else {
                foreach ($cell->content as $key => $type) {
                    if (in_array($key, static::$CELLS)) {
                        $types[$name][] = $key;
                    } elseif (in_array($key, static::$CELL_TYPES)) {
                        $key = 'Storage ' . $type;
                        $types[$name][] = $key;
                    } else {
                        $types[$name] = array();
                    }
                }
            }
        }
        $typekeys = array_keys($types);
        $cell_width = strlen($typekeys[count($typekeys)-1])+1;

        foreach ($this->get_unique_section('GridMap/Cells')->content as $cellk => $cell) {
            for ($i = 0; $i < strlen($cell); $i += $cell_width) {
                $char = trim(substr($cell, $i, $cell_width));
                if (in_array($char, array_keys($types))) {
                    foreach ($types[$char] as $type) {
                        if (!isset($cells[$type])) {
                            $cells[$type] = 1;
                        } else {
                            $cells[$type]++;
                        }
                    }
                }
            }
        }
        if (isset($cells['Habitation'])) {
            $cells['HabitationCapacity'] = (int) floor($cells['Habitation']/9);
        }

        return $cells;
    }

    /**
     * Retrieve the count of a certain type of object on the ship.
     *
     * @param string $label the object to retrieve
     *
     * @return int the number of objects of the given type
     */
    public function get_object_count($label) {
        if (!$this->section_exists('Objects')) {
            return 0;
        }

        return count($this->get_object_content($label));
    }

    /**
     * Retrieve the content of a certain type of object on the ship.
     *
     * @param string $label the object to retrieve
     * @param string $item  a particular property of the objects to retrieve
     *
     * @return ($item is null ? array<string[]> : array<string>) the content information
     */
    public function get_object_content($label, $item = null) {
        if (!$this->section_exists('Objects')) {
            return array();
        }
        $content = array();
        foreach ($this->get_unique_section('Objects')->sections as $object) {
            if (is_array($object)) {
                $object = $object[count($object)-1];
            }
            if ($object->content['Type'] == $label) {
                if ($item != null) {
                    if (isset($object->content[$item])) {
                        $content[] = $object->content[$item];
                    } else {
                        continue;
                    }
                } else {
                    $content[] = $object->content;
                }
            }
        }

        return $content;
    }
}
