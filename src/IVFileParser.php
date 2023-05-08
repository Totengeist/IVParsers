<?php

/**
 * Classes necessary for creating a base IVFile.
 *
 * A standard Introversion configuration file is built out of recursively defined sections all
 * following the same format, therefore, we consider a whole file to be a root section.
 */

namespace Totengeist\IVParser;

/**
 * A section of a standard Introversion configuration file.
 *
 * A Section includes all metadata and sub-sections within it, implementing a relative pathing
 * structure for retrieving specific sub-sections.
 */
class Section {
    /** The section path, relative to the base. */
    public $path = null;
    /** The original content used to generate the section. */
    public $content = null;
    /** An array of subsection objects. */
    public $sections = [];

    /**
     * Create a new Section.
     *
     * Since most IVFiles, and therefore Sections, are created directly from files, we set the
     * indentation level for processing. This isn't necessary if creating an IVFile programatically.
     *
     * @param string $path     the name of the section
     * @param array  $content  the structure of the section and its subsections
     * @param int    $level    the indentation level of the section in the original file
     * @param array  $subfiles an array of IVFile-inheriting classes and their paths
     */
    public function __construct($path = null, $content = null, $level = 0, $subfiles = []) {
        if ($path !== null) {
            $this->path = $path;
        }
        if ($content !== null) {
            $this->get_sections($content, $level, $subfiles);
        }
    }

    /**
     * Print a "tree" of sections and subsections.
     *
     * This is primarily a debug function and prints sections paths recursively.
     *
     * @param array $ignore paths to ignore in the tree
     */
    public function print_section_tree($ignore = []) {
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

    /**
     * Create subsections recursively based on a given structure.
     *
     * @param array $content  the structure of the section and its subsections
     * @param int   $level    the indentation level of the section in the original file
     * @param array $subfiles an array of IVFile-inheriting classes and their paths
     */
    public function get_sections($structure, $level = 0, $subfiles = []) {
        $content = [];
        $start = -1;
        $key = null;
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
                            echo __LINE__ . $line;
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
                            $this->add_section($section_title, new Section($this->path . '/' . $section_title, []));
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
                        echo __LINE__ . ' ' . $i . ' ' . $line;
                    } else {
                        $content[trim($matches['name'])] = trim(trim($matches['value']), '"');
                    }
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
     * @param string $path     the name of the section
     * @param array  $content  the structure of the section and its subsections
     * @param int    $level    the indentation level of the section in the original file
     * @param array  $subfiles an array of IVFile-inheriting classes and their paths
     *
     * @return Section[] the Section or a Section-inheriting class
     */
    public function check_subfile($path, $content, $level, $subfiles) {
        if (in_array($path, array_keys($subfiles))) {
            return new $subfiles[$path]($content, $level, $subfiles);
        }

        return new Section($path, $content, $level, $subfiles);
    }

    /**
     * Add a subsection.
     *
     * @param string    $key    a string representing the section name
     * @param Section[] $object the section to add
     *
     * @return void
     */
    public function add_section($key, $object) {
        if (isset($this->sections[$key])) {
            if (gettype($this->sections[$key]) == 'array') {
                $this->sections[$key][] = $object;
            } else {
                $this->sections[$key] = [$this->sections[$key], $object];
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
        $section = $this->get_section($section_path);
        if ($section == null) {
            return false;
        }

        return true;
    }

    /**
     * Get a specific subsection.
     *
     * @param string $section_path a relative path to a subsection
     *
     * @return Section[]|array|null the specific subsection(s) requested or null if not found
     */
    public function get_section($section_path) {
        $path = explode('/', $section_path);
        $content = $this; // Relative pathing starts from the current section.
        // Move down the path until we find what we want or a part of the path is missing.
        foreach ($path as $section) {
            if (!isset($content->sections[$section])) {
                return null;
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
    public $file = null;

    /**
     * An intermediary constructor.
     *
     * The constructor for an IVFile handles parsing of data sent directory form a file (i.e. a
     * string) whereas a Section requires an array. The usual constructor is then called on that
     * data.
     *
     * @param array $structure the structure of the section and its subsections
     * @param int   $level     the indentation level of the section in the original file
     * @param array $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = null, $level = 0, $subfiles = []) {
        if ($structure !== null) {
            if (is_string($structure)) {
                $structure = preg_split('/\r?\n/', $structure);
            }
            parent::__construct('', $structure, $level, $subfiles);
        }
    }
}
