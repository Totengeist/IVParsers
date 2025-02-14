<?php

namespace Totengeist\IVParser\TheLastStarship\Bugs;

use Totengeist\IVParser\TheLastStarship\SaveFile;
use Totengeist\IVParser\TheLastStarship\ShipFile;
use Totengeist\IVParser\TheLastStarship\TLSBug;

/**
 * The base class for all bugs the parsers can fix.
 *
 * A variety of functions that each bug must have to function properly.
 */
class TiddletBug extends TLSBug {
    /**
     * A name for the bug.
     *
     * @var string
     */
    protected static $NAME = 'Tiddlet Bug';
    /**
     * A description of the bug.
     *
     * @var string
     */
    protected static $DESCRIPTION = 'Tiddlets that sustained damage are not accepted by The Trouble with Tiddlets and the mission gets stuck.';
    /** @var bool does the bug apply to Ship files? */
    protected static $IS_SHIP_BUG = true;
    /** @var bool does the bug apply to Save files? */
    protected static $IS_SAVE_BUG = true;

    /**
     * Check if the current file is affected by this bug.
     *
     * @param ShipFile|SaveFile $file the ship or save to analyze
     *
     * @return bool
     */
    public static function hasBug($file) {
        if (get_class($file) == "Totengeist\IVParser\TheLastStarship\ShipFile") {
            $tiddlets = $file->getObjectContent('Tiddlet', 'Damage');
            foreach ($tiddlets as $tiddlet) {
                if (((float) $tiddlet) > 0.0) {
                    return true;
                }
            }
        } elseif (get_class($file) == "Totengeist\IVParser\TheLastStarship\SaveFile") {
            /** @var ShipFile[] $ships */
            $ships = $file->getShips('FriendlyShip');
            foreach ($ships as $ship) {
                if (static::hasBug($ship)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Attempt to fix the bug.
     *
     * @param ShipFile|SaveFile $file the ship or save to fix
     *
     * @return bool was the bug successfully resolved?
     */
    public static function resolveBug(&$file) {
        if (get_class($file) == "Totengeist\IVParser\TheLastStarship\ShipFile") {
            foreach ($file->getUniqueSection('Objects')->sections as $object) {
                if (is_array($object)) {
                    $object = $object[count($object)-1];
                }
                if ($object->content['Type'] == 'Tiddlet' && isset($object->content['Damage'])) {
                    $object->content['Damage'] = '0';
                }
            }
        } elseif (get_class($file) == "Totengeist\IVParser\TheLastStarship\SaveFile") {
            $ships = $file->getShips('FriendlyShip');
            if (count($ships) == 0) {
                return true;
            }
            foreach ($ships as $ship) {
                static::resolveBug($ship);
            }
        }

        return !static::hasBug($file);
    }
}
