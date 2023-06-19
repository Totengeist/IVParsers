<?php

namespace Tests;

use Totengeist\IVParser\TheLastStarship\ShipFile;

class ShipFileTest extends TestCase {
    public const EXAMPLE_SHIP = '
Id                   0  
Name                 Empty  
Type                 FriendlyShip  
Mass                 2648.000  
Interior             200.0000  
Toughness            264.0000  
TimeIndex            143.08454437500001  
BEGIN GridMap    
    Width                240  
    Height               160  
    BEGIN Palette    
        BEGIN .          END
        BEGIN ,          Hull true  Interior true  Habitation true  Floor true  END
        BEGIN -          Hull true  END
        BEGIN +          Hull true  Interior true  END
        BEGIN /          Hull true  HullShape TopRightFill  END
        BEGIN a          Hull true  HullShape TopLeftFill  END
        BEGIN b          Hull true  HullShape BottomRightFill  END
        BEGIN c          Hull true  HullShape BottomLeftFill  END
        BEGIN d          Hull true  Storage Cargo  END
    END
    BEGIN Cells      
        row72                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . b - - - - - - - - - - - - - - c . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row73                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row74                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row75                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row76                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row77                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row78                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row79                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row80                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row81                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row82                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row83                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row84                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row85                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row86                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . - , , , , , , , , , , , , , , - . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row87                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . / - - - - - + + + + - - - - - a . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
        row88                ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . / - - - - - - a . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . "  
    END
END
BEGIN Networks   END
BEGIN WorkQueue  Workers "[2, 3, 4, 5, 6]"  END
BEGIN Welfare    Timer 47.940788269042969  END
BEGIN Habitation SewageTimer 0.068042416666656891  END
BEGIN Deliveries 
    Timer                0.055459458380937576  
    IdleTime             5.0047349929809570  
    BEGIN Trade      END
END
';

    public function testCanCreateEmptyShipFile() {
        $file = new ShipFile();
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
    }

    public function testCanCheckValidShipStructure() {
        $this->assertTrue(ShipFile::is_valid_structure(self::EXAMPLE_SHIP));
    }

    public function testCanCheckInvalidShipStructure() {
        $ship = str_replace("BEGIN Habitation SewageTimer 0.068042416666656891  END\n", '', self::EXAMPLE_SHIP);
        $this->assertFalse(ShipFile::is_valid_structure($ship));
    }

    public function testCanCreateFullShipFile() {
        $file = new ShipFile(self::EXAMPLE_SHIP);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Deliveries'));
        $this->assertEquals($file->get_section('Deliveries')->content['Timer'], '0.055459458380937576');
    }

    public function testCanCreateShipWithObjectsSection() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 0]"      
        Id                   9  
        Type                 DockingPort  
        BEGIN Slots      
            Size                 4  
            BEGIN "[i 0]"      X 3  END
            BEGIN "[i 1]"      X 2  END
            BEGIN "[i 2]"      X 1  END
            BEGIN "[i 3]"      END
        END
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_object_count('DockingPort'), 1);
        $this->assertEquals($file->get_object_content('DockingPort'), array(array('Id' => '9', 'Type' => 'DockingPort')));
        $this->assertEquals($file->get_object_content('DockingPort', 'Id'), array('9'));
    }

    public function testDuplicateObjectIdsRetrieveLastObject() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 0]"      
        Id                   9  
        Type                 GatlingGun  
    END
    BEGIN "[i 0]"      
        Id                   9  
        Type                 DockingPort  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_object_count('GatlingGun'), 0);
        $this->assertEquals($file->get_object_count('DockingPort'), 1);
        $this->assertEquals($file->get_object_content('DockingPort'), array(array('Id' => '9', 'Type' => 'DockingPort')));
        $this->assertEquals($file->get_object_content('DockingPort', 'Id'), array('9'));
    }

    public function testCanQueryObjectsOnShipWithoutObjectsSection() {
        $file = new ShipFile(self::EXAMPLE_SHIP);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertFalse($file->section_exists('Objects'));
        $this->assertEquals($file->get_object_count('NotExistGun'), 0);
        $this->assertEquals($file->get_object_content('NotExistGun'), array());
        $this->assertEquals(count($file->get_weapons()), 0);
    }

    public function testCanGetItemsByTypeWithString() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 TinyTank  
    END
    BEGIN "[i 2]"      
        Type                 SmallTank  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_items_by_type('TinyTank'), array(
            'TinyTank' => array(array('Type'=>'TinyTank')),
        ));
    }

    public function testCanGetItemCountsByType() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 TinyTank  
    END
    BEGIN "[i 2]"      
        Type                 SmallTank  
    END
    BEGIN "[i 3]"      
        Type                 Tank  
    END
    BEGIN "[i 4]"      
        Type                 TinyTank  
    END
    BEGIN "[i 5]"      
        Type                 SmallTank  
    END
    BEGIN "[i 6]"      
        Type                 SmallTank  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_item_counts_by_type(array('TinyTank', 'SmallTank', 'Tank')), array(
            'TinyTank' => 2,
            'SmallTank' => 3,
            'Tank' => 1,
        ));
    }

    public function testCanGetWeapons() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    BEGIN "[i 8]"      
        Id                   21785  
        Type                 GatlingGun  
        Position.x           99.50000  
        Position.y           11.00000  
        Toughness            60.00000  
        CornerX              98  
        CornerY              10  
        Width                3  
        Height               2  
        Job                  202  
        BEGIN Ports      
            Size                 1  
            BEGIN "[i 0]"      Type Power  X 1  Y 1  NetworkId 1  Demand 1.000000  END
        END
        BEGIN Slots      
            Size                 2  
            BEGIN "[i 0]"      Y 1  END
            BEGIN "[i 1]"      X 2  Y 1  END
        END
        BEGIN Turret     Layer 21752  Type GatlingGun  Position.x 99.50000  Position.y 10.50000  BarrelAngle -0.06795105  END
    END
    BEGIN "[i 9]"      
        Id                   21786  
        Type                 GatlingGun  
        Position.x           95.50000  
        Position.y           11.00000  
        Toughness            60.00000  
        CornerX              98  
        CornerY              10  
        Width                3  
        Height               2  
        Job                  135  
        BEGIN Ports      
            Size                 1  
            BEGIN "[i 0]"      Type Power  X 1  Y 1  NetworkId 1  Demand 1.000000  END
        END
        BEGIN Slots      
            Size                 2  
            BEGIN "[i 0]"      Y 1  END
            BEGIN "[i 1]"      X 2  Y 1  END
        END
        BEGIN Turret     Layer 21752  Type GatlingGun  Position.x 99.50000  Position.y 10.50000  BarrelAngle -0.06795105  END
    END
    BEGIN "[i 171]"    
        Id                   22221  
        Type                 Cannon  
        Position.x           97.00000  
        Position.y           65.50000  
        Toughness            90.00000  
        CornerX              96  
        CornerY              64  
        Width                2  
        Height               3  
        Orientation          Left  
        Job                  129  
        TargetOffset.x       180.2905  
        TargetOffset.y       73.15503  
        BEGIN Ports      
            Size                 1  
            BEGIN "[i 0]"      Type Power  X 1  Y 2  NetworkId 1  Demand 1.000000  END
        END
        BEGIN Slots      
            Size                 1  
            BEGIN "[i 0]"      X 1  Y 1  END
        END
        BEGIN Turret     Layer 21752  Type Cannon  Position.x 96.50000  Position.y 65.50000  Orientation.x -1.000000  Orientation.y 0.0000000  BarrelAngle 0.08282086  END
    END
    BEGIN "[i 125]"    
        Id                   22086  
        Type                 Railgun  
        Position.x           17.00000  
        Position.y           63.50000  
        Toughness            60.00000  
        CornerX              16  
        CornerY              62  
        Width                2  
        Height               3  
        Orientation          Right  
        Job                  242  
        BEGIN Ports      
            Size                 1  
            BEGIN "[i 0]"      Type Power  NetworkId 1  Demand 1.000000  END
        END
        BEGIN Slots      
            Size                 1  
            BEGIN "[i 0]"      Y 1  END
        END
        BEGIN Turret     Layer 21752  Type Railgun  Position.x 17.50000  Position.y 63.50000  Orientation.x 1.000000  Orientation.y 0.0000000  END
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_object_count('GatlingGun'), 2);
        $this->assertEquals($file->get_object_count('NotExistGun'), 0);
        $this->assertEquals($file->get_object_content('NotExistGun'), array());
        $this->assertEquals($file->get_object_content('Railgun'), array(array(
            'Id' => '22086',
            'Type' => 'Railgun',
            'Position.x' => '17.00000',
            'Position.y' => '63.50000',
            'Toughness' => '60.00000',
            'CornerX' => '16',
            'CornerY' => '62',
            'Width' => '2',
            'Height' => '3',
            'Orientation' => 'Right',
            'Job' => '242',
        )));
        $this->assertEquals(count($file->get_weapons()), 3);
    }

    public function testCanGetEngines() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 Engine  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_engines(), array(
            'Engine' => array(array('Type'=>'Engine')),
        ));
    }

    public function testCanGetLogistics() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 MiningLaser  
    END
    BEGIN "[i 2]"      
        Type                 DroneBay  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_logistics(), array(
            'DroneBay' => array(array('Type'=>'DroneBay')),
            'MiningLaser' => array(array('Type'=>'MiningLaser')),
        ));
    }

    public function testCanGetThrusters() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 Thruster  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_thrusters(), array(
            'Thruster' => array(array('Type'=>'Thruster')),
        ));
    }

    public function testCanGetTankCapacitiesByResource() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 TinyTank  
        Resource             Fuel  
        Capacity             6000.000  
    END
    BEGIN "[i 2]"      
        Type                 SmallTank  
        Resource             Oxygen  
        Capacity             9000.000  
    END
    BEGIN "[i 3]"      
        Type                 Tank  
        Resource             Water  
        Capacity             12000.000  
    END
    BEGIN "[i 4]"      
        Type                 TinyTank  
        Resource             Sewage  
        Capacity             6000.000  
    END
    BEGIN "[i 5]"      
        Type                 SmallTank  
        Resource             WasteWater  
        Capacity             9000.000  
    END
    BEGIN "[i 6]"      
        Type                 Tank  
        Resource             CarbonDioxide  
        Capacity             12000.000  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_tank_capacity_by_type(), array(
            'Fuel' => 6000.0,
            'Oxygen' => 9000.0,
            'Water' => 12000.0,
            'Sewage' => 6000.0,
            'WasteWater' => 9000.0,
            'CarbonDioxide' => 12000.0,
            'Deuterium' => 0.0,
        ));
    }

    public function testCanGetGeneratorCountAndCapacity() {
        $ship = self::EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 158]"    
        Type                 Reactor  
        Position.x           61.50000  
        Position.y           72.50000  
        Toughness            90.00000  
        CornerX              60  
        CornerY              71  
        Width                3  
        Height               3  
        Orientation          Left  
        Activation           1.000000  
        Activated            true  
        PowerOutput          2.835065  
    END
    BEGIN "[i 449]"    
        Id                   1221  
        Type                 FusionReactor  
        Position.x           222.5000  
        Position.y           80.50000  
        Toughness            250.0000  
        CornerX              220  
        CornerY              78  
        Width                5  
        Height               5  
        Charge               10000.00  
        Activation           1.000000  
        Activated            true  
        PowerOutput          50.00000  
    END
    BEGIN "[i 450]"    
        Id                   1222  
        Type                 FusionReactor  
        Position.x           222.5000  
        Position.y           80.50000  
        Toughness            250.0000  
        CornerX              220  
        CornerY              78  
        Width                5  
        Height               5  
        Charge               10000.00  
        Activation           1.000000  
        Activated            true  
        PowerOutput          50.00000  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), "Totengeist\IVParser\TheLastStarship\ShipFile");
        $this->assertTrue($file->section_exists('Objects'));
        $this->assertEquals($file->get_generator_count_and_output(), array(102.835065, array('Reactor' => 1, 'FusionReactor' => 2)));
    }

    public function testCanGetCellInfo() {
        $ship = str_replace('        BEGIN ,          Hull true  Interior true  Habitation true  Floor true  END', '        BEGIN ,          Hull false  Interior false  Habitation flase  Floor false  END
        BEGIN ,          Hull true  Interior true  Habitation true  Floor true  END', self::EXAMPLE_SHIP);
        $file = new ShipFile($ship);
        $this->assertEquals($file->get_cell_info(), array(
            'Hull' => 258,
            'Interior' => 200,
            'Habitation' => 196,
            'Floor' => 196,
            'HabitationCapacity' => 21,
        ));
    }
}
