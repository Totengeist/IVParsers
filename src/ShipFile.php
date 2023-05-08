<?php

namespace Totengeist\IVParser;

/**
 * Classes necessary for processing a `.ship` file.
 */
class ShipFile extends IVFile {
    public $info = [];

    const WEAPONS = ['GatlingGun', 'Cannon', 'Railgun'];
    const ENGINES = ['Engine'];
    const POWER = ['Reactor', 'FusionReactor'];
    const LOGISTICS = ['MiningLaser', 'DroneBay'];
    const THRUSTERS = ['Thrusters'];
    const CELLS = ['Hull', 'Interior', 'Floor', 'Habitation', 'Armour'];
    const CELL_TYPES = ['Storage'];
    const TANKS = ['TinyTank', 'Small Tank', 'Tank'];
    const RESOURCES = ['Fuel', 'Oxygen', 'Water', 'Sewage', 'WasteWater', 'CarbonDioxide', 'Deuterium'];

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid ship file before calling the standard constructor.
     *
     * @param array $structure the structure of the section and its subsections
     * @param int   $level     the indentation level of the section in the original file
     * @param array $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = null, $level = 0, $subfiles = []) {
        if (!self::is_ship($structure, $level)) {
            throw new \Exception('This is not a ship file.');
        }
        parent::__construct($structure, $level, $subfiles);

        $this->get_info();
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
     * Collect the relevant ship info.
     *
     * This should be replaced by multiple functions and not use a class member variable for
     * gathering.
     */
    public function get_info() {
        $this->info = [];
        $this->info['Type'] = $this->content['Type'];
        $this->info['Name'] = $this->content['Name'];
        $this->info['Engines'] = 0;
        $this->info['PowerOutput'] = 0;
        $this->info['Weapons'] = [];
        $this->info['Structure'] = [];
        $this->info['Storage'] = [];
        $this->info['TankCapacity'] = [];

        foreach (self::RESOURCES as $resource) {
            $this->info['TankCapacity'][$resource] = 0;
        }
        $this->info['Mass'] = isset($this->content['Mass']) ? (float) $this->content['Mass'] : 0;
        foreach (self::WEAPONS as $weapon) {
            if (!isset($this->info[$weapon])) {
                $this->info['Weapons'][$weapon] = $this->get_object_count($weapon);
            } else {
                $this->info['Weapons'][$weapon] += $this->get_object_count($weapon);
            }
        }
        foreach (self::ENGINES as $engine) {
            $this->info['Engines'] += $this->get_object_count($engine);
        }
        foreach (self::POWER as $generator) {
            if (!isset($this->info[$generator])) {
                $this->info[$generator] = $this->get_object_count($generator);
            } else {
                $this->info[$generator] += $this->get_object_count($generator);
            }
            foreach ($this->get_object_content($generator, 'PowerOutput') as $output) {
                $this->info['PowerOutput'] += (float) $output;
            }
        }
        foreach (self::LOGISTICS as $item) {
            $this->info[$item] = $this->get_object_count($item);
        }
        foreach (self::TANKS as $tank) {
            foreach ($this->get_object_content($tank) as $output) {
                if (isset($output['Resource']) && isset($output['Capacity'])) {
                    $this->info['TankCapacity'][$output['Resource']] += (float) $output['Capacity'];
                }
            }
        }
        foreach ($this->get_cell_info() as $key => $cell) {
            $shortkey = str_replace('Storage ', '', $key);
            if ($key == $shortkey) {
                $this->info['Structure'][$key] = $cell;
            } else {
                $this->info['Storage'][$shortkey] = $cell;
            }
        }
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
        $types = [];
        $cells = [];
        foreach ($this->get_section('GridMap/Palette')->sections as $cell) {
            $path = explode('/', $cell->path);
            $name = $path[count($path)-1];
            // If the name is empty, the character is '/' and was exploded.
            if ($name == '') {
                $name = '/';
            }
            // If the cell is empty space, it contains no content.
            if (count($cell->content) == 0) {
                $types[$name] = [];
            } else {
                foreach ($cell->content as $key => $type) {
                    if (in_array($key, self::CELLS)) {
                        $types[$name][] = $key;
                    } elseif (in_array($key, self::CELL_TYPES)) {
                        $key = 'Storage ' . $type;
                        $types[$name][] = $key;
                    } else {
                        $types[$name] = [];
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
            return [];
        }
        $content = [];
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

    /**
     * Debug print fucntion that should be replaced with something more useful.
     */
    public function print_info() {
        $info = $this->info;
        ksort($info);
        $info['Weapons'] = 0;
        foreach (self::WEAPONS as $weapon) {
            if (isset($info[$weapon])) {
                $info['Weapons'] += $info[$weapon];
            }
        }
        $info['PowerGenerators'] = 0;
        foreach (self::POWER as $gen) {
            $info['PowerGenerators'] += $info[$gen];
        }
        $template = 'Your ship is named %s. It has %3d weapons and %3d engines. Its %3d power generators generate %01.2f Mw.';
        echo sprintf($template,
            $info['Name'],
            $info['Weapons'],
            $info['Engines'],
            $info['PowerGenerators'],
            $info['PowerOutput']
        );
        print_r($info);
    }
}
