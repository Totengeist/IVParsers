<?php

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Bug;

/**
 * The base class for all bugs the The Last Starship parsers can fix.
 *
 * A variety of functions that each bug must have to function properly.
 */
abstract class TLSBug extends Bug {
    /** @var bool does the bug apply to Ship files? */
    const IS_SHIP_BUG = false;
    /** @var bool does the bug apply to Save files? */
    const IS_SAVE_BUG = false;

    /**
     * Check if the bug applies to Ship files.
     *
     * @return bool
     */
    public static function isShipBug() {
        return static::IS_SHIP_BUG;
    }

    /**
     * Check if the bug applies to Save files.
     *
     * @return bool
     */
    public static function isSaveBug() {
        return static::IS_SAVE_BUG;
    }
}
