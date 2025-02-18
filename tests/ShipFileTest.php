<?php

namespace Tests;

use Totengeist\IVParser\TheLastStarship\ShipFile;

class ShipFileTest extends TestCase {
    /** @var string */
    public static $FILE_CLASS = "Totengeist\IVParser\TheLastStarship\ShipFile";
    /**
     * @var string
     *
     * @SuppressWarnings("php:S1131")
     */
    public static $EXAMPLE_SHIP = '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
    }

    public function testCanCheckValidShipStructure() {
        $this->assertTrue(ShipFile::isValidStructure(static::$EXAMPLE_SHIP));
    }

    public function testCanCheckInvalidShipStructure() {
        $ship = str_replace('BEGIN Habitation SewageTimer 0.068042416666656891  END', '', static::$EXAMPLE_SHIP);
        $this->assertFalse(ShipFile::isValidStructure($ship));
    }

    public function testCanCreateFullShipFile() {
        $file = new ShipFile(static::$EXAMPLE_SHIP);
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Deliveries'));
        $this->assertEquals($file->getSection('Deliveries')->content['Timer'], '0.055459458380937576');
    }

    public function testCanCreateShipWithObjectsSection() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getObjectCount('DockingPort'), 1);
        $this->assertEquals($file->getObjectContent('DockingPort'), array(array('Id' => '9', 'Type' => 'DockingPort')));
        $this->assertEquals($file->getObjectContent('DockingPort', 'Id'), array('9'));
    }

    public function testDuplicateObjectIdsRetrieveLastObject() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getObjectCount('GatlingGun'), 0);
        $this->assertEquals($file->getObjectCount('DockingPort'), 1);
        $this->assertEquals($file->getObjectContent('DockingPort'), array(array('Id' => '9', 'Type' => 'DockingPort')));
        $this->assertEquals($file->getObjectContent('DockingPort', 'Id'), array('9'));
    }

    public function testCanQueryObjectsOnShipWithoutObjectsSection() {
        $file = new ShipFile(static::$EXAMPLE_SHIP);
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertFalse($file->sectionExists('Objects'));
        $this->assertEquals($file->getObjectCount('NotExistGun'), 0);
        $this->assertEquals($file->getObjectContent('NotExistGun'), array());
        $this->assertEquals(count($file->getWeapons()), 0);
    }

    public function testCanGetItemsByTypeWithString() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getItemsByType('TinyTank'), array(
            'TinyTank' => array(array('Type'=>'TinyTank')),
        ));
    }

    public function testCanGetItemCountsByType() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getItemCountsByType(array('TinyTank', 'SmallTank', 'Tank')), array(
            'TinyTank' => 2,
            'SmallTank' => 3,
            'Tank' => 1,
        ));
    }

    public function testCanGetWeapons() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getObjectCount('GatlingGun'), 2);
        $this->assertEquals($file->getObjectCount('NotExistGun'), 0);
        $this->assertEquals($file->getObjectContent('NotExistGun'), array());
        $this->assertEquals($file->getObjectContent('Railgun'), array(array(
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
        $this->assertEquals(count($file->getWeapons()), 3);
    }

    public function testCanGetEngines() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 Engine  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getEngines(), array(
            'Engine' => array(array('Type'=>'Engine')),
        ));
    }

    public function testCanGetLogistics() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getLogistics(), array(
            'DroneBay' => array(array('Type'=>'DroneBay')),
            'MiningLaser' => array(array('Type'=>'MiningLaser')),
        ));
    }

    public function testCanGetThrusters() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
BEGIN Objects    
    Size                 1  
    BEGIN "[i 1]"      
        Type                 Thruster  
    END
END';

        $file = new ShipFile($ship);
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getThrusters(), array(
            'Thruster' => array(array('Type'=>'Thruster')),
        ));
    }

    public function testCanGetTankCapacitiesByResource() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getTankCapacityByType(), array(
            'Fuel' => 6000.0,
            'Oxygen' => 9000.0,
            'Water' => 12000.0,
            'Sewage' => 6000.0,
            'WasteWater' => 9000.0,
            'CarbonDioxide' => 12000.0,
            'Deuterium' => 0.0,
            'MetreonGas' => 0.0,
            'RefinedMetreon' => 0.0,
            'HyperspaceIsotopes' => 0.0,
            'StableIsotopes' => 0.0,
        ));
    }

    public function testCanGetGeneratorCountAndCapacity() {
        /**
         * @SuppressWarnings("php:S1131")
         */
        $ship = static::$EXAMPLE_SHIP . '
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
        $this->assertEquals(get_class($file), static::$FILE_CLASS);
        $this->assertTrue($file->sectionExists('Objects'));
        $this->assertEquals($file->getGeneratorCountAndOutput(), array(102.835065, array('Reactor' => 1, 'FusionReactor' => 2)));
    }

    public function testCanGetCellInfo() {
        $ship = str_replace('        BEGIN ,          Hull true  Interior true  Habitation true  Floor true  END', '        BEGIN ,          Hull false  Interior false  Habitation false  Floor false  END
        BEGIN ,          Hull true  Interior true  Habitation true  Floor true  END', static::$EXAMPLE_SHIP);
        $file = new ShipFile($ship);
        $this->assertEquals($file->getCellInfo(), array(
            'Hull' => 258,
            'Interior' => 200,
            'Habitation' => 196,
            'Floor' => 196,
            'HabitationCapacity' => 21,
        ));
    }

    public function testCanGetAndSetMetaData() {
        $file = new ShipFile(static::$EXAMPLE_SHIP);
        $this->assertEquals(0, $file->getId());
        $this->assertEquals('Empty', $file->getName());
        $this->assertEquals('', $file->getAuthor());
        $this->assertEquals('FriendlyShip', $file->getType());
        $this->assertEquals(array(0.0, 0.0), $file->getPosition());
        $this->assertEquals(0.0, $file->getRotation());

        $file->setId(235);
        $file->setName('Science Vessel');
        $file->setAuthor('John Doe');
        $file->setType('Hostile');
        $file->setPosition(34.1234, 12.483);
        $file->setRotation(123.54);

        $this->assertEquals(235, $file->getId());
        $this->assertEquals('Science Vessel', $file->getName());
        $this->assertEquals('John Doe', $file->getAuthor());
        $this->assertEquals('Hostile', $file->getType());
        $this->assertEquals(array(34.1234, 12.483), $file->getPosition());
        $this->assertEquals(123.54, $file->getRotation());
    }
}
