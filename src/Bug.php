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
    protected static $name = 'Untitled';
    /**
     * A description of the bug.
     *
     * @var string
     */
    protected static $description = 'No description provided.';

    /**
     * Retrieve metadata for the bug.
     *
     * @return string[]
     */
    public static function getMetadata() {
        return array(static::$name, static::$description);
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
