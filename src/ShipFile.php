<?php

namespace Totengeist\IVParser;

/**
 * Classes necessary for processing a `.ship` file.
 */
class ShipFile extends IVFile {
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

    const WEAPONS = array('GatlingGun', 'Cannon', 'Railgun');
    const ENGINES = array('Engine');
    const POWER = array('Reactor', 'FusionReactor');
    const LOGISTICS = array('MiningLaser', 'DroneBay');
    const THRUSTERS = array('Thrusters');
    const CELLS = array('Hull', 'Interior', 'Floor', 'Habitation', 'Armour');
    const CELL_TYPES = array('Storage');
    const TANKS = array('TinyTank', 'Small Tank', 'Tank');
    const RESOURCES = array('Fuel', 'Oxygen', 'Water', 'Sewage', 'WasteWater', 'CarbonDioxide', 'Deuterium');

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid ship file before calling the standard constructor.
     *
     * @param array $structure the structure of the section and its subsections
     * @param int   $level     the indentation level of the section in the original file
     * @param array $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        if (!self::is_ship($structure, $level)) {
            throw new \Exception('This is not a ship file.');
        }
        parent::__construct($structure, $level, $subfiles);
    }

    /**
     * Verify the given structure is a save file.
     *
     * We check for the Habitation section as a unique section to save files.
     *
     * @param array $structure the structure of the section and its subsections
     * @param int   $level     the indentation level of the section in the original file
     *
     * @return bool is it a valid ship file?
     */
    public static function is_ship($structure, $level) {
        if (is_string($structure)) {
            $structure = preg_split('/\r?\n/', $structure);
        }
        foreach ($structure as $line) {
            if (strpos($line, str_repeat('    ', $level) . 'BEGIN Habitation') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the overall tank capacity for each type of resource.
     *
     * @return array an associative array of resource capacities
     */
    public function get_tank_capacity_by_type() {
        $tanks = array();

        foreach (self::RESOURCES as $resource) {
            $tanks[$resource] = 0.0;
        }

        foreach ($this->get_items_by_type(self::TANKS) as $tank_type) {
            foreach ($tank_type as $tank) {
                if (isset($tank['Resource']) && isset($tank['Capacity'])) {
                    if (!isset($tanks[$tank['Resource']])) {
                        $tanks[$tank['Resource']] = 0.0;
                    }
                    $tanks[$tank['Resource']] += (float) $tank['Capacity'];
                }
            }
        }

        return $tanks;
    }

    /**
     * Get the count of generators and their overall output.
     *
     * @return array
     */
    public function get_generator_count_and_output() {
        $output = 0;
        $count = array();
        foreach (self::POWER as $generator) {
            $count[$generator] = 0;
            foreach ($this->get_object_content($generator, 'PowerOutput') as $power) {
                $output += (float) $power;
                $count[$generator]++;
            }
        }

        return array($output, $count);
    }

    /**
     * Get items by type given an associative array of types.
     *
     * @return array the items, grouped by type
     */
    public function get_items_by_type($type) {
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
     * Get the counts of items by type given an associative array of types.
     *
     * @return array the item counts, grouped by type
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
     * @return array the cell counts by type
     */
    public function get_cell_info() {
        $types = array();
        $cells = array();
        foreach ($this->get_section('GridMap/Palette')->sections as $cell) {
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
                    if (in_array($key, self::CELLS)) {
                        $types[$name][] = $key;
                    } elseif (in_array($key, self::CELL_TYPES)) {
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

        foreach ($this->get_section('GridMap/Cells')->content as $cellk => $cell) {
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
            $cells['HabitationCapacity'] = floor($cells['Habitation']/9);
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
        $count = 0;
        foreach ($this->get_section('Objects')->sections as $object) {
            if ($object->content['Type'] == $label) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Retrieve the content of a certain type of object on the ship.
     *
     * @param string $label the object to retrieve
     * @param string $item  a particular property of the objects to retrieve
     *
     * @return array the content information
     */
    public function get_object_content($label, $item = null) {
        if (!$this->section_exists('Objects')) {
            return array();
        }
        $content = array();
        foreach ($this->sections['Objects']->sections as $object) {
            if ($object->content['Type'] == $label) {
                if ($item != null && isset($object->content[$item])) {
                    $content[] = $object->content[$item];
                } else {
                    $content[] = $object->content;
                }
            }
        }

        return $content;
    }

    // @codeCoverageIgnoreStart

    /**
     * Debug print function that should be replaced with something more useful.
     */
    public function print_info() {
        $info = array();
        $info['Type'] = $this->content['Type'];
        $info['Name'] = $this->content['Name'];
        $info['Mass'] = isset($this->content['Mass']) ? (float) $this->content['Mass'] : 0;
        $info['Engines'] = $this->get_item_counts_by_type(self::ENGINES);
        $info['Weapons'] = $this->get_item_counts_by_type(self::WEAPONS);
        $info['Logistics'] = $this->get_item_counts_by_type(self::LOGISTICS);
        $result = $this->get_generator_count_and_output();
        $info['PowerOutput'] = $result[0];
        $info['Generators'] = $result[1];
        $info['TankCapacity'] = $this->get_tank_capacity_by_type();
        foreach ($this->get_cell_info() as $key => $cell) {
            $shortkey = str_replace('Storage ', '', $key);
            if ($key == $shortkey) {
                $info['Structure'][$key] = $cell;
            } else {
                $info['Storage'][$shortkey] = $cell;
            }
        }

        ksort($info);
        $weapon_count = 0;
        foreach ($info['Weapons'] as $count) {
            $weapon_count += $count;
        }
        $engine_count = 0;
        foreach ($info['Engines'] as $count) {
            $weapon_count += $count;
        }
        $generators = 0;
        foreach ($info['Generators'] as $count) {
            $generators += $count;
        }
        $template = 'Your ship is named %s. It has %3d weapons and %3d engines. Its %3d power generators generate %01.2f Mw.';
        echo sprintf($template,
            $info['Name'],
            $weapon_count,
            $info['Engines'],
            $generators,
            $info['PowerOutput']
        );
        print_r($info);
    }

    // @codeCoverageIgnoreEnd
}
