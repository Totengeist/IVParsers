<?php

namespace Totengeist\IVParser;

/**
 * A class for interacting with bugs.
 *
 * A variety of functions that each bug must have to function properly.
 */
abstract class BugManager {
    /** @var string[] A list of class paths for available bugs */
    protected static $BUGS = array(
        'Totengeist\IVParser\TheLastStarship\Bugs\TiddletBug',
    );

    /**
     * Retrieve a list of supported bugs for a particular game.
     *
     * @param string $game the namespace of the game as used in the project
     *
     * @return string[] an array of bug names
     */
    public static function get_bugs_from_game($game) {
        $game = 'Totengeist\IVParser\\' . $game;
        $bugs = array();
        foreach (static::$BUGS as $bug) {
            if (strpos($bug, $game) === 0) {
                $bugs[] = $bug;
            }
        }

        return $bugs;
    }
}
