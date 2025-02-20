<?php

namespace Tests;

use Totengeist\IVParser\TheLastStarship\SaveFile;

class SaveFileTest extends TestCase {
    /** @var string */
    public static $fileClass = "Totengeist\IVParser\TheLastStarship\SaveFile";
    /**
     * @var string
     *
     * @SuppressWarnings("php:S1131")
     */
    public static $exampleGalaxy = 'BEGIN Galaxy     
    SectorCount          10  
    EntrySystem          171  
    ExitSystem           174  
    CurrentSystem        167  
    TargetSystem         168  
    NextId               179  
    Position.x           1372.000  
    Position.y           653.0000  
    VoidPosition.x       -1680.000  
    VoidPosition.y       1575.000  
    VoidRadius           2908.040  
    PrevSectorPosition.x -840.0000  
    PrevSectorPosition.y 1312.500  
    NextSectorPosition.x 2520.000  
    NextSectorPosition.y -262.5000  
    BEGIN Objects    
        Size                 12  
        BEGIN "[i 0]"      Id 167  Position.x 1372.000  Position.y 653.0000  Radius 15.82564  Name NG167  Colony true  END
        BEGIN "[i 1]"      Id 168  Position.x 1344.000  Position.y 295.0000  Radius 13.74719  Name NG168  Hostiles true  END
        BEGIN "[i 2]"      Id 169  Position.x 199.0000  Position.y 246.0000  Radius 14.31433  Name NG169  Hostiles true  END
        BEGIN "[i 3]"      Id 170  Position.x 1559.000  Position.y 501.0000  Radius 14.77779  Name NG170  Colony true  END
    END
    BEGIN Hazards    
        Size                 1  
        BEGIN "[i 0]"      Position.x 799.1553  Position.y 723.8403  Radius 122.0737  Rotation 357.0007  Type 1  END
    END
END
';
    /**
     * @var string
     *
     * @SuppressWarnings("php:S1131")
     */
    public static $exampleSave = '
TimeIndex            27024.592115973996  
NextId               40951  
DeltaTime            0.0074211573382854112  
PlayTime             21291.003685735108  
BEGIN HUD        Camera.x 86.38315  Camera.y -221.5498  Rotation -94.47211  ViewSize 122.5608  Layer 19074  LastFriendlyLayer 19074  TrackLayer true  TrackPosition.x 101.0257  TrackPosition.y -212.3031  TrackRotation 265.5279  END
BEGIN Trade      
    Cash                 9054477  
    TradePossible        true  
    Inflation            1.766237  
    BEGIN OurStock   
        FusionReactor        1  
        TinyTank             1  
        SpaceSuitPod         1  
    END
    BEGIN TheirStock 
        Reactor              1  
        Battery              3  
        Tank                 2  
    END
    BEGIN Trade      END
END
BEGIN Orders     END
BEGIN Score      
    BEGIN Details    
        Size                 11  
        BEGIN "[i 0]"      Population 30  END
        BEGIN "[i 1]"      Population 30  END
        BEGIN "[i 2]"      Population 30  END
        BEGIN "[i 3]"      Population 34  END
        BEGIN "[i 4]"      Population 41  END
        BEGIN "[i 5]"      Population 41  END
        BEGIN "[i 6]"      Population 41  END
        BEGIN "[i 7]"      Population 49  END
        BEGIN "[i 8]"      Population 58  END
        BEGIN "[i 9]"      Population 191  END
        BEGIN "[i 10]"     Population 132  END
    END
END
BEGIN Physics    END
BEGIN Missions   
    NextId               573  
    BEGIN Missions   
        Size                 1  
        BEGIN "[i 0]"      
            Id                   5  
            Type                 SpecialScienceObjective  
            Title                scienceobjective_title  
            FromSystemId         1  
            Successes            35188  
            Accepted             true  
            Updated              true  
            AssignedLayerId      0  
            ReceivedSensors      true  
            InstalledSensors     true  
            Items                "[20, 23]"  
            BEGIN Log        
                Size                 16  
                BEGIN "[i 0]"      StringId scienceobjective_description1  END
                BEGIN "[i 1]"      StringId scienceobjective_description2  END
                BEGIN "[i 2]"      StringId scienceobjective_description3  END
                BEGIN "[i 3]"      StringId mission_assignedtolayer  Player true  END
                BEGIN "[i 4]"      StringId scienceobjective_accepted1  END
                BEGIN "[i 5]"      StringId scienceobjective_accepted2  END
                BEGIN "[i 6]"      StringId scienceobjective_receivedsensors1  END
                BEGIN "[i 7]"      Id InstallSensors  StringId scienceobjective_installsensors  ValueX 2  Objective true  END
                BEGIN "[i 8]"      Id PowerSensors  StringId scienceobjective_installsensors2  Objective true  END
                BEGIN "[i 9]"      StringId scienceobjective_sensorsinstalled  Player true  END
                BEGIN "[i 10]"     StringId scienceobjective_usesensors1  END
                BEGIN "[i 11]"     StringId scienceobjective_usesensors2  END
                BEGIN "[i 12]"     StringId scienceobjective_successbonus  ValueX 10  ValueY 10000  ValueZ 100  Player true  END
                BEGIN "[i 13]"     StringId scienceobjective_successbonus  ValueX 100  ValueY 100000  ValueZ 1000  Player true  END
                BEGIN "[i 14]"     StringId scienceobjective_successbonus  ValueX 1000  ValueY 1000000  ValueZ 10000  Player true  END
                BEGIN "[i 15]"     Id Data  StringId scienceobjective_capturedata  ValueX 35188  Objective true  END
            END
        END
    END
END
BEGIN Story      SensorData 35188.95  Evacuated 965  END
BEGIN Weather    END
BEGIN Ratings    Industry 74  END
BEGIN Layer      
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
END
';

    public function testCanCreateEmptySaveFile() {
        $file = new SaveFile();
        $this->assertEquals(static::$fileClass, get_class($file));
    }

    public function testCanCheckValidSaveStructure() {
        $this->assertTrue(SaveFile::isValidStructure(static::$exampleSave . static::$exampleGalaxy));
    }

    public function testCanCheckInvalidSaveStructure() {
        $this->assertFalse(SaveFile::isValidStructure(static::$exampleSave));
    }

    public function testCanCreateFullSaveFile() {
        $file = new SaveFile(static::$exampleSave . static::$exampleGalaxy);
        $this->assertTrue($file->sectionExists('Galaxy'));
        $this->assertEquals('171', $file->getSection('Galaxy')->content['EntrySystem']);
    }

    public function testCanGetSaveVersion() {
        $file = new SaveFile(static::$exampleSave . static::$exampleGalaxy);
        $this->assertEquals(0, $file->getSaveVersion());

        /**
         * @SuppressWarnings("php:S1131")
         */
        $save = "SaveVersion                   4  \n" . static::$exampleSave . static::$exampleGalaxy;

        $file = new SaveFile($save);
        $this->assertEquals(4, $file->getSaveVersion());
    }

    public function testCanGetShips() {
        $file = new SaveFile(static::$exampleSave . static::$exampleGalaxy);
        $ship = $file->getShips();
        $friendlyShips = $file->getShips('FriendlyShip');

        $this->assertTrue($file->sectionExists('Layer'));

        $this->assertEquals(1, count($file->getShips()));
        $this->assertEquals(ShipFileTest::$fileClass, get_class($ship[0]));

        $this->assertEquals(1, count($file->getShips('FriendlyShip')));
        $this->assertEquals(ShipFileTest::$fileClass, get_class($friendlyShips[0]));

        $this->assertEquals(0, count($file->getShips('HostileShip')));
        $this->assertEquals(array(), $file->getShips('HostileShip'));
    }

    public function testCanGetMissions() {
        $file = new SaveFile(static::$exampleSave . static::$exampleGalaxy);
        $mission = $file->getMissions();

        $this->assertTrue($file->sectionExists('Missions/Missions'));
        $this->assertEquals(1, count($file->getMissions()));
        $this->assertEquals('SpecialScienceObjective', $mission[0]->content['Type']);
    }

    public function testCanGetGalaxyInfo() {
        $file = new SaveFile(static::$exampleSave . static::$exampleGalaxy);
        $galaxy = $file->getGalaxyInfo();

        $this->assertTrue($file->sectionExists('Galaxy'));
        $this->assertEquals(171, $galaxy['EntrySystem']);
    }

    public function testCanGetAndSetMetaData() {
        $file = new SaveFile(static::$exampleSave . static::$exampleGalaxy);

        $this->assertEquals('Survival', $file->getGameMode());
        $this->assertEquals(false, $file->hasMultiSystemSimulation());

        $file->setGameMode('Industry');
        $file->enableMultiSystemSimulation();

        $this->assertEquals('Industry', $file->getGameMode());
        $this->assertEquals(true, $file->hasMultiSystemSimulation());

        $file->disableMultiSystemSimulation();
        $this->assertEquals(false, $file->hasMultiSystemSimulation());
    }
}
