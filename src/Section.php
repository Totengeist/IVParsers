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
    /** @var bool whether the section is an array */
    public $array = false;
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
            $pretext = $this->indent($level, ''); // deal with any indentation of the text
            if ($start == -1) {
                if (strpos($line, $pretext . 'BEGIN') === 0) {
                    if (strpos($line, 'END') === (strlen(rtrim($line))-3)) {
                        $this->process_singleline_section($line);
                    } else {
                        // section continues
                        $matches = $this->match_or_exception('/^ *BEGIN ([^ "]*|"[^"]*") *$/i', $line);
                        $key = $this->array_check($matches[1]);
                        $start = $i+1;
                    }
                } else {
                    $matches = $this->match_or_exception('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/', trim($line));
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
     * Check whether the section is an array.
     *
     * @param string $title
     *
     * @return int|string
     */
    public function array_check($title) {
        $title = trim($title);
        preg_match("/^\"\[i (\d+)\]\"$/i", $title, $title_check);
        if ($title_check) {
            $this->array = true;
            $title = (int) $title_check[1];
        }

        return $title;
    }

    /**
     * Throw an exception when a preg_match doesn't match.
     *
     * @param string $regex
     * @param string $line
     *
     * @return string[]
     */
    public function match_or_exception($regex, $line) {
        preg_match($regex, $line, $matches);
        if (empty($matches)) {
            // @codeCoverageIgnoreStart
            throw new InvalidFileException();
            // @codeCoverageIgnoreEnd
        }

        return $matches;
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
     * Process a single-line section.
     *
     * @param string $line the line to create a section from
     *
     * @return void
     */
    public function process_singleline_section($line) {
        $matches = $this->match_or_exception('/^ *BEGIN ([^ "]*|"[^"]*")(?: +(.*))? +END *$/i', $line);
        $section_title = $this->array_check($matches[1]);
        $section_content = isset($matches[2]) ? trim($matches[2]) : '';
        if ($section_content == '') {
            $this->add_section($section_title, new Section($this->path . '/' . $section_title, array()));
        } else {
            preg_match_all('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/i', $section_content, $content_check);
            $this->add_section($section_title, new Section($this->path . '/' . $section_title, $content_check[0]));
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
     * @return Section the specific unique section requested
     */
    public function get_unique_section($section_path) {
        $section = $this->get_section($section_path);
        if (is_array($section)) {
            /** @var Section */
            $section = end($section);

            return $section;
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
     * @return Section[] the specific repeatable section requested
     */
    public function get_repeatable_section($section_path) {
        $section = $this->get_section($section_path);
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

    /**
     * Convert the section into a string.
     *
     * @param string $path      the label to assign to the section
     * @param int    $level     the indentation level
     * @param int    $col_width the column width to pad the label to
     * @param bool   $in_array  is this section part of an array?
     *
     * @return string The section converted to a string
     */
    public function toString($path = '', $level = -1, $col_width = 1, $in_array = false) {
        $content_key_length = 2;
        $section_key_length = 1;
        foreach (array_keys($this->content) as $content_key) {
            $length = (int) ceil(strlen($content_key)/10);
            if ($length > $content_key_length) {
                $content_key_length = $length;
            }
        }
        foreach (array_keys($this->sections) as $section_key) {
            $length = (int) ceil(strlen($section_key)/10);
            if ($length > $section_key_length) {
                $section_key_length = $length;
            }
        }

        $string = '';
        $end = '';

        if ($level !== -1) {
            if ($in_array) {
                $path = "\"[i $path]\" ";
                $string = $this->indent($level, 'BEGIN ' . str_pad($path, 10*$col_width+2) . ' ');
            } else {
                $string = $this->indent($level, 'BEGIN ' . str_pad($path, 10*$col_width) . ' ');
            }
            $end = $this->indent($level, "END\n");
        }

        if ($this->sections === array() && $this->content === array()) {
            if ($in_array) {
                return $this->indent($level, 'BEGIN ' . str_pad($path, 10*$col_width+2) . " END\n");
            }

            return $this->indent($level, 'BEGIN ' . str_pad($path, 10*$col_width) . " END\n");
        }

        if (count($this->sections) == 0 && count($this->content) < 11) {
            $end = "END\n";
            foreach ($this->content as $key => $content) {
                $string .= "$key " . $this->quote($content) . '  ';
            }
        } else {
            foreach ($this->content as $key => $content) {
                $string .= "\n" . $this->indent($level + 1, str_pad($key, 10*$content_key_length) . ' ' . $this->quote($content) . '  ');
            }
            $string .= "\n";
            foreach ($this->sections as $key => $section) {
                if (is_array($section)) {
                    foreach ($section as $sub) {
                        $string .= $sub->toString($key, $level + 1, $section_key_length, $this->array);
                    }
                } else {
                    $string .= $section->toString($key, $level + 1, $section_key_length, $this->array);
                }
            }
        }

        return $string . $end;
    }

    /**
     * Indent a string.
     *
     * @param int    $indent the indentation level
     * @param string $line   the string to indent
     *
     * @return string
     */
    public function indent($indent, $line) {
        return str_repeat('    ', $indent) . $line;
    }

    /**
     * Quote a string depending on if it has spaces.
     *
     * @param string $line the string to quote
     *
     * @return string
     */
    public function quote($line) {
        if (strpos($line, ' ') !== false) {
            return '"' . $line . '"';
        }

        return $line;
    }

    /**
     * Convert the section into a string.
     *
     * @return string The section converted to a string
     */
    public function __toString() {
        return $this->toString();
    }
}
