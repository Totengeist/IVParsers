<?php

/**
 * Classes necessary for creating a base IVFile.
 *
 * A standard Introversion configuration file is built out of recursively defined sections all
 * following the same format, therefore, we consider a whole file to be a root section.
 */

namespace Totengeist\IVParser;

use Totengeist\IVParser\Exception\InvalidFileException;

/**
 * The implementation of a standard Introversion configuration file.
 *
 * The IVFile class does some standard handling of user/file inputs to avoid repeating this for every
 * subsection.
 */
class IVFile extends Section {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $REQUIRED_SECTIONS = array();
    /** @var string[] content items that must exist for it to be a valid file */
    protected static $REQUIRED_CONTENT = array();
    /** @var string the file type identifier */
    protected static $FILE_TYPE = 'application/introversion';

    /**
     * An intermediary constructor.
     *
     * The constructor for an IVFile handles parsing of data sent directory form a file (i.e. a
     * string) whereas a Section requires an array. The usual constructor is then called on that
     * data.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct('', self::prepare_structure($structure), $level, $subfiles);
    }

    /**
     * An intermediary constructor.
     *
     * The constructor for an IVFile handles parsing of data sent directly from a file (i.e. a
     * string) whereas a Section requires an array. The usual constructor is then called on that
     * data.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     *
     * @return string[] a cleaned structure
     */
    public static function prepare_structure($structure) {
        if (is_string($structure)) {
            $result = preg_split('/\r?\n/', $structure);
            $structure = ($result === false) ? array($structure) : $result;
        }

        return $structure;
    }

    /**
     * Verify the given structure is a save file.
     *
     * We check for sections and content required in the given file.
     *
     * @return bool is it a valid file?
     */
    public function is_valid() {
        foreach (static::$REQUIRED_CONTENT as $content) {
            if (!isset($this->content[$content])) {
                return false;
            }
        }
        foreach (static::$REQUIRED_SECTIONS as $section) {
            if (!$this->section_exists($section)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify the given structure is valid.
     *
     * We check to make sure the file can be parsed, regardless of required content or sections.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]|null   $subfiles  an array of IVFile-inheriting classes and their paths
     *
     * @return bool is it a valid file structure?
     */
    public static function is_valid_structure($structure, $level = 0, $subfiles = null) {
        try {
            $class = get_called_class();
            if ($subfiles === null) {
                $file = new $class($structure, $level);
            } else {
                $file = new $class($structure, $level, $subfiles);
            }
        } catch (InvalidFileException $ex) {
            return false;
        }

        return true;
    }

    /**
     * Get an identifier for which IVFile variant we're using.
     *
     * @return string the file type
     */
    public function file_type() {
        return static::$FILE_TYPE;
    }

    /**
     * Check if the file is a valid Introversion file, then find if it's a supported type.
     *
     * @param string $file the file to check
     *
     * @return string|false the file type or false
     */
    public static function check_file_type($file) {
        try {
            new IVFile($file);
        } catch (InvalidFileException $e) {
            return false;
        }
        $files = glob(__DIR__ . '/../src/**/*File.php');
        if ($files === false) {
            return false;
        }
        foreach ($files as $class_file) {
            $class = str_replace(__DIR__ . '/../src/', '', $class_file);
            $class = str_replace('.php', '', $class);
            $class = '\\Totengeist\\IVParser\\' . str_replace('/', '\\', $class);
            if ($class == '\\Totengeist\\IVParser\\IVFile') {
                continue;
            }
            if (!class_exists($class)) {
                include_once $class_file;
            }
            try {
                $iv_file = new $class($file);
            } catch (InvalidFileException $e) {
                continue;
            }

            return $iv_file->file_type(); /* @phpstan-ignore-line */
        }

        return static::$FILE_TYPE;
    }
}
