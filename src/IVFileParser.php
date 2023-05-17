<?php

/**
 * Classes necessary for creating a base IVFile.
 *
 * A standard Introversion configuration file is built out of recursively defined sections all
 * following the same format, therefore, we consider a whole file to be a root section.
 */

namespace Totengeist\IVParser;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\Exception\SectionNotFoundException;

/**
 * A section of a standard Introversion configuration file.
 *
 * A Section includes all metadata and sub-sections within it, implementing a relative pathing
 * structure for retrieving specific sub-sections.
 */
class Section {
    /** @var string the section's path, relative to the base */
    public $path = '';
    /** @var string[] the section's meta data */
    public $content = array();
    /** @var Section[]|Section[][] the section's subsections */
    public $sections = array();

    /**
     * Create a new Section.
     *
     * Since most IVFiles, and therefore Sections, are created directly from files, we set the
     * indentation level for processing. This isn't necessary if creating an IVFile programatically.
     *
     * @param string   $path      the name of the section
     * @param string[] $structure the structure of the section and its subsections
     * @param int      $level     the indentation level of the section in the original file
     * @param string[] $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($path = '', $structure = array(), $level = 0, $subfiles = array()) {
        $this->path = $path;
        if ($structure !== array()) {
            $this->get_sections($structure, $level, $subfiles);
        }
    }

    // @codeCoverageIgnoreStart

    /**
     * Print a "tree" of sections and subsections.
     *
     * This is primarily a debug function and prints sections paths recursively.
     *
     * @param string[] $ignore paths to ignore in the tree
     *
     * @return void
     */
    public function print_section_tree($ignore = array()) {
        if (in_array($this->path, $ignore)) {
            return;
        }
        echo $this->path . "\n";
        foreach ($this->sections as $section) {
            if (is_array($section)) {
                foreach ($section as $sec) {
                    $sec->print_section_tree($ignore);
                }
            } else {
                $section->print_section_tree($ignore);
            }
        }
    }

    // @codeCoverageIgnoreEnd

    /**
     * Create subsections recursively based on a given structure.
     *
     * @param string[] $structure the structure of the section and its subsections
     * @param int      $level     the indentation level of the section in the original file
     * @param string[] $subfiles  an array of IVFile-inheriting classes and their paths
     *
     * @return void
     */
    public function get_sections($structure, $level = 0, $subfiles = array()) {
        $content = array();
        $start = -1;
        $key = '';
        for ($i = 0; $i < count($structure); $i++) {
            $line = $structure[$i];
            if (trim($line) == '') {
                continue;
            }
            $pretext = str_repeat('    ', $level); // deal with any indentation of the text
            if ($start == -1) {
                if (strpos($line, $pretext . 'BEGIN') === 0) {
                    if (strpos($line, 'END') === (strlen(rtrim($line))-3)) {
                        // section ends in the same line
                        preg_match('/^ *BEGIN ([^ "]*|"[^"]*")(?: +(.*))? +END *$/i', $line, $matches);
                        if (count($matches) == 0) {
                            // @codeCoverageIgnoreStart
                            throw new \ErrorException('Something unexpected happened. This file either contains new structures or is faulty.', 0, E_ERROR, __FILE__, __LINE__);
                            // @codeCoverageIgnoreEnd
                        }
                        $section_title = trim($matches[1]);
                        preg_match("/^\"\[i ([0-9]+)\]\"$/i", $section_title, $title_check);
                        if ($title_check) {
                            $section_title = (int) $title_check[1];
                        }
                        if (!isset($matches[2])) {
                            $section_content = '';
                        } else {
                            $section_content = trim($matches[2]);
                        }
                        if ($section_content == '') {
                            $this->add_section($section_title, new Section($this->path . '/' . $section_title, array()));
                        } else {
                            preg_match_all('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/i', $section_content, $content_check);
                            $this->add_section($section_title, new Section($this->path . '/' . $section_title, $content_check[0]));
                        }
                    } else {
                        // section continues
                        preg_match('/^ *BEGIN ([^ "]*|"[^"]*") *$/i', $line, $matches);
                        $key = trim($matches[1]);
                        preg_match("/^\"\[i ([0-9]+)\]\"$/i", $key, $title_check);
                        if ($title_check) {
                            $key = (int) $title_check[1];
                        }
                        $start = $i+1;
                    }
                } else {
                    preg_match('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/', trim($line), $matches);
                    if (count($matches) == 0) {
                        // @codeCoverageIgnoreStart
                        throw new \ErrorException('Something unexpected happened. This file either contains new structures or is faulty.', 0, E_ERROR, __FILE__, __LINE__);
                        // @codeCoverageIgnoreEnd
                    }
                    $content[trim($matches['name'])] = trim(trim($matches['value']), '"');
                }
            } else {
                // in a section
                if (strpos($line, $pretext . 'END') === 0) {
                    $this->add_section($key, $this->check_subfile($this->path . '/' . $key, array_slice($structure, $start, $i - $start), $level+1, $subfiles));
                    $start = -1;
                }
            }
        }
        $this->content = $content;
    }

    /**
     * Check if the content is a subfile.
     *
     * Check if the section path is in the list of subfile paths. If so, create the subfile.
     * Otherwise, create a generic section.
     *
     * @param string   $path     the name of the section
     * @param string[] $content  the structure of the section and its subsections
     * @param int      $level    the indentation level of the section in the original file
     * @param string[] $subfiles an array of IVFile-inheriting classes and their paths
     *
     * @return Section the Section or a Section-inheriting class
     */
    public function check_subfile($path, $content, $level, $subfiles) {
        if (in_array($path, array_keys($subfiles))) {
            /** @var Section $object */
            $object = new $subfiles[$path]($content, $level, $subfiles);

            return $object;
        }

        return new Section($path, $content, $level, $subfiles);
    }

    /**
     * Add a subsection.
     *
     * @param int|string $key    a string representing the section name
     * @param Section    $object the section to add
     *
     * @return void
     */
    public function add_section($key, $object) {
        if (isset($this->sections[$key])) {
            if (gettype($this->sections[$key]) == 'array') {
                $this->sections[$key][] = $object;
            } else {
                $this->sections[$key] = array($this->sections[$key], $object);
            }
        } else {
            $this->sections[$key] = $object;
        }
    }

    /**
     * Check if a specific subsection exists.
     *
     * Checking for existence of a subsection requires the same work as retrieving it, so we attempt
     * that and see if we get anything back.
     *
     * @param string $section_path a relative path to a subsection
     *
     * @return bool
     */
    public function section_exists($section_path) {
        try {
            $section = $this->get_section($section_path);
        } catch (SectionNotFoundException $ex) {
            return false;
        }

        return true;
    }

    /**
     * Ensure only a single section is returned.
     *
     * Most sections in an Introversion file are unique. Only a few are repeatable. Here we ensure
     * that the last section found in the file is chosen when only a single section is expected.
     *
     * @param string $section_path a relative path to a subsection
     *
     * @return Section the specific unique section requested or null if not found
     */
    public function get_unique_section($section_path) {
        $section = $this->get_section($section_path);
        if ($section === array()) {
            throw new SectionNotFoundException();
        }
        if (is_array($section)) {
            return $section[count($section)-1];
        }

        return $section;
    }

    /**
     * Ensure an array of sections is returned, even if only one exists.
     *
     * Some sections in an Introversion file are inteded to repeat. Here we ensure that an array is
     * is returned even if only one file is found.
     *
     * @param string $section_path a relative path to a subsection
     *
     * @return Section[] the specific unique section requested or null if not found
     */
    public function get_repeatable_section($section_path) {
        $section = $this->get_section($section_path);
        if ($section === array()) {
            throw new SectionNotFoundException();
        }
        if (!is_array($section)) {
            return array($section);
        }

        return $section;
    }

    /**
     * Get a specific subsection.
     *
     * @todo support looking inside arrays
     *
     * @param string $section_path a relative path to a subsection
     *
     * @return Section|Section[] the specific subsection(s) requested or an empty array if not found
     */
    public function get_section($section_path) {
        $path = explode('/', $section_path);
        $content = $this; // Relative pathing starts from the current section.
        // Move down the path until we find what we want or a part of the path is missing.
        foreach ($path as $section) {
            if (!isset($content->sections[$section])) {
                throw new SectionNotFoundException($section_path);
            }
            $content = $content->sections[$section];
        }

        return $content;
    }
}

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
     * The constructor for an IVFile handles parsing of data sent directory form a file (i.e. a
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
     * Verify the given structure is a save file.
     *
     * We check for sections and content required in the given file.
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
}
