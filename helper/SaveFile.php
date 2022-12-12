<?php

namespace IVParser;

use IVParser\IVFile;
use IVParser\ShipFile;

class SaveFile extends IVFile {
    public $info = [];

    const SHIPS = ['FriendlyShip', 'HostileShip', 'ShipForSale', 'NeutralShip', 'Derelict'];
    
    public function __construct($structure = null, $level = 0, $subfiles = ['/Layer' => ShipFile::class]) {
        if( !$this->is_save($structure, $level) ) {
            throw new \Exception('This is not a save file.');
        }
        parent::__construct($structure, $level, $subfiles);
    }
    
    public function is_save($structure, $level) {
        if( is_string($structure) ) {
            $structure = preg_split('/\r?\n/', $structure);
        }
        foreach( $structure as $line ) {
            if (strpos($line, str_repeat('    ', $level) . 'BEGIN Galaxy') === 0) {
                return true;
            }
        }
        return false;
    }
    
    public function get_layers() {
        if( $this->section_exists("Layer") ) {
            $section = $this->get_section("Layer");
            if( is_array( $section ) ) {
                return $section;
            } else {
                return [$section];
            }
        }
        return [];
    }
    
    public function get_ships($type = null) {
        $ships = [];
        $layers = $this->get_layers();
        
        foreach($layers as $ship) {
            $ships[$ship->info["Type"]][] = $ship;
        }
        
        if( $type !== null ) {
            if (isset($ships[$type]) ) {
                return $ships[$type];
            } else {
                return [];
            }
        }
        
        return $ships;
    }
    
    public function get_missions() {
        if( $this->section_exists("Layer") ) {
            $missions = [];
            foreach( $this->get_section("Missions/Missions")->sections as $mission) {
                $missions[] = $mission;
            }
            return $missions;
        }
        return [];
    }
    
    public function get_galaxy_info() {
        $info = [];
        $galaxy = $this->get_section("Galaxy")->content;
        $info['SectorCount'] = isset($galaxy["SectorCount"]) ? intval($galaxy["SectorCount"]): 0;
        $info['CurrentSystem'] = intval($galaxy["CurrentSystem"]);
        $info['EntrySystem'] = intval($galaxy["EntrySystem"]);
        $info['ExitSystem'] = intval($galaxy["ExitSystem"]);
        return $info;
    }
    
    public function get_save_version() {
        if( isset( $this->content['SaveVersion'] ) ) {
            return intval($this->content['SaveVersion']);
        }
        return 0;
    }
    
    public function print_info() {
        if( $this->section_exists("Layer") ) {
            $all_ships = count($this->get_layers());
            $fleet = $this->get_ships('FriendlyShip');
            $fleet_cnt = count($fleet);
            $hostiles = count($this->get_ships('HostileShip'));
            $ships = [];
            foreach( $fleet as $ship) {
                $ships[] = $ship->info["Name"];
            }
        } else {
            $all_ships = 0;
            $fleet_cnt = 0;
            $hostiles = 0;
            $ships = [];
        }
        $galaxy = $this->get_galaxy_info();
        
        $template = 'Your verison %d save file has %2d active missions, %2d ships in your fleet. The current sector is %2d. The current system (%3d) has %2d neutral and %2d hostile ships. Your fleet contains: %s.';
        echo sprintf($template, 
            $this->get_save_version(),
            count($this->get_missions()),
            $fleet_cnt,
            $galaxy["SectorCount"],
            $galaxy["CurrentSystem"],
            $all_ships-$fleet_cnt-$hostiles,
            $hostiles,
            implode(", ", $ships)
        );
    }
}
