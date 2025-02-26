<?php

/**
 * Classes necessary for processing a `.ship` file.
 */

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\IVFile;

class ShipFile extends IVFile {
    protected static $requiredSections = array('Habitation');
    protected static $fileType = 'application/tls-ship+introversion';
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
    public static $ships = array('FriendlyShip', 'HostileShip', 'ShipForSale', 'NeutralShip', 'Derelict');

    /** @var string[] */
    public static $weapons = array('GatlingGun', 'Cannon', 'Railgun');
    /** @var string[] */
    public static $engines = array('Engine');
    /** @var string[] */
    public static $power = array('Reactor', 'FusionReactor');
    /** @var string[] */
    public static $logistics = array('MiningLaser', 'DroneBay');
    /** @var string[] */
    public static $thrusters = array('Thruster');
    /** @var string[] */
    public static $cells = array('Hull', 'Interior', 'Floor', 'Habitation', 'Armour');
    /** @var string[] */
    public static $cellTypes = array('Storage');
    /** @var string[] */
    public static $tanks = array('TinyTank', 'SmallTank', 'Tank');
    /** @var string[] */
    public static $resources = array('Fuel', 'Oxygen', 'Water', 'Sewage', 'WasteWater', 'CarbonDioxide', 'Deuterium', 'MetreonGas', 'RefinedMetreon', 'HyperspaceIsotopes', 'StableIsotopes');

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
        if (!$this->isValid() && $structure !== array()) {
            throw new InvalidFileException('ship');
        }
    }

    /**
     * Get the ship's ID.
     *
     * @return int the ID of the ship
     */
    public function getId() {
        return (int) $this->content['Id'];
    }

    /**
     * Get the ship's name.
     *
     * @return string the name of the ship
     */
    public function getName() {
        return $this->content['Name'];
    }

    /**
     * Get the ship's author.
     *
     * @return string the name of the ship's author
     */
    public function getAuthor() {
        return isset($this->content['Author']) ? $this->content['Author'] : '';
    }

    /**
     * Get the ship's type.
     *
     * @return string the name of the ship's type
     */
    public function getType() {
        return isset($this->content['Type']) ? $this->content['Type'] : '';
    }

    /**
     * Get the ship's position.
     *
     * @return float[] an array containing the x and y position of the ship
     */
    public function getPosition() {
        return array(
            isset($this->content['Offset.x']) ? (float) $this->content['Offset.x'] : 0.0,
            isset($this->content['Offset.y']) ? (float) $this->content['Offset.y'] : 0.0
        );
    }

    /**
     * Get the ship's rotation.
     *
     * @return float the rotation of the ship
     */
    public function getRotation() {
        return isset($this->content['Rotation']) ? (float) $this->content['Rotation'] : 0.0;
    }

    /**
     * Get the ship file's save version number.
     *
     * Save files during the playtest did not have version number, so we return 0 if no version is
     * found.
     *
     * @return int the version of the ship file
     */
    public function getSaveVersion() {
        if (isset($this->content['SaveVersion'])) {
            return intval($this->content['SaveVersion']);
        }

        return 0;
    }

    /**
     * Change the ship's ID.
     *
     * @param int $id the ID of the ship
     *
     * @return void
     */
    public function setId($id) {
        $this->content['Id'] = "$id";
    }

    /**
     * Change the ship's name.
     *
     * @param string $name the name of the ship
     *
     * @return void
     */
    public function setName($name) {
        $this->content['Name'] = $name;
    }

    /**
     * Change the ship's author.
     *
     * @param string $author the name of the ship's author
     *
     * @return void
     */
    public function setAuthor($author) {
        $this->content['Author'] = $author;
    }

    /**
     * Change the ship's type.
     *
     * @param string $type the type of ship
     *
     * @return void
     */
    public function setType($type) {
        $this->content['Type'] = $type;
    }

    /**
     * Change the ship's position.
     *
     * @param float $x the x position of the ship
     * @param float $y the y position of the ship
     *
     * @return void
     */
    public function setPosition($x, $y) {
        $this->content['Offset.x'] = "$x";
        $this->content['Offset.y'] = "$y";
    }

    /**
     * Change the ship's rotation.
     *
     * @param float $rotation the rotation of the ship
     *
     * @return void
     */
    public function setRotation($rotation) {
        $this->content['Rotation'] = "$rotation";
    }

    /**
     * Get the overall tank capacity for each type of resource.
     *
     * @return float[] an associative array of resource capacities
     */
    public function getTankCapacityByType() {
        $tanks = array();

        foreach (static::$resources as $resource) {
            $tanks[$resource] = 0.0;
        }

        foreach ($this->getTanks() as $tank_type) {
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
    public function getGeneratorCountAndOutput() {
        $output = 0;
        $count = array();
        foreach ($this->getGenerators() as $type => $generators) {
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
    public function getItemsByType($type) {
        if (is_string($type)) {
            $type = array($type);
        }
        $items = array();

        foreach ($type as $item) {
            $results = $this->getObjectContent($item);
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
    public function getWeapons() {
        return $this->getItemsByType(static::$weapons);
    }

    /**
     * Get engines by type.
     *
     * @return array<string, array<string[]>> engines, grouped by type
     */
    public function getEngines() {
        return $this->getItemsByType(static::$engines);
    }

    /**
     * Get logistics equipment by type.
     *
     * @return array<string, array<string[]>> logistics equipment, grouped by type
     */
    public function getLogistics() {
        return $this->getItemsByType(static::$logistics);
    }

    /**
     * Get generators by type.
     *
     * @return array<string, array<string[]>> generators, grouped by type
     */
    public function getGenerators() {
        return $this->getItemsByType(static::$power);
    }

    /**
     * Get thrusters by type.
     *
     * @return array<string, array<string[]>> thrusters, grouped by type
     */
    public function getThrusters() {
        return $this->getItemsByType(static::$thrusters);
    }

    /**
     * Get tanks by type.
     *
     * @return array<string, array<string[]>> tanks, grouped by type
     */
    public function getTanks() {
        return $this->getItemsByType(static::$tanks);
    }

    /**
     * Get the counts of items by type given an associative array of types.
     *
     * @param string[] $type a list of the objects to retrieve
     *
     * @return int[] the item counts, grouped by type
     */
    public function getItemCountsByType($type) {
        $counts = array();

        foreach ($type as $key) {
            $counts[$key] = 0;
        }

        foreach ($this->getItemsByType($type) as $key => $content) {
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
    public function getCellInfo() {
        $types = array();
        $cells = array();
        foreach ($this->getUniqueSection('GridMap/Palette')->sections as $cell) {
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
                    if (in_array($key, static::$cells)) {
                        $types[$name][] = $key;
                    } elseif (in_array($key, static::$cellTypes)) {
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

        foreach ($this->getUniqueSection('GridMap/Cells')->content as $cell) {
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
    public function getObjectCount($label) {
        if (!$this->sectionExists('Objects')) {
            return 0;
        }

        return count($this->getObjectContent($label));
    }

    /**
     * Retrieve the content of a certain type of object on the ship.
     *
     * @param string $label the object to retrieve
     * @param string $item  a particular property of the objects to retrieve
     *
     * @return ($item is null ? array<string[]> : array<string>) the content information
     */
    public function getObjectContent($label, $item = null) {
        if (!$this->sectionExists('Objects')) {
            return array();
        }
        $content = array();
        foreach ($this->getUniqueSection('Objects')->sections as $object) {
            if (is_array($object)) {
                $object = $object[count($object)-1];
            }
            if ($object->content['Type'] == $label) {
                if ($item != null) {
                    if (isset($object->content[$item])) {
                        $content[] = $object->content[$item];
                    }
                } else {
                    $content[] = $object->content;
                }
            }
        }

        return $content;
    }
}
