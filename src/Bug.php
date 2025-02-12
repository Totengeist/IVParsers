<?php

namespace Totengeist\IVParser;

/**
 * The base class for all bugs the parsers can fix.
 *
 * A variety of functions that each bug must have to function properly.
 */
abstract class Bug {
    /**
     * A name for the bug.
     *
     * @var string
     */
    protected static $NAME = 'Untitled';
    /**
     * A description of the bug.
     *
     * @var string
     */
    protected static $DESCRIPTION = 'No description provided.';

    /**
     * Retrieve metadata for the bug.
     *
     * @return string[]
     */
    public static function get_metadata() {
        return array(static::$NAME, static::$DESCRIPTION);
    }

    /**
     * Check if the current file is affected by this bug.
     *
     * @param IVFile $file the file to analyze
     *
     * @return bool
     */
    abstract public static function hasBug($file);

    /**
     * Attempt to fix the bug.
     *
     * @param IVFile $file the file to fix
     *
     * @return bool was the bug successfully resolved?
     */
    abstract public static function resolveBug(&$file);
}
