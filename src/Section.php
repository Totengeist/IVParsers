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
            $this->getSections($structure, $level, $subfiles);
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
    public function getSections($structure, $level = 0, $subfiles = array()) {
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
                        $this->processSingleLineSection($line);
                    } else {
                        // section continues
                        $matches = $this->matchOrFail('/^ *BEGIN ([^ "]*|"[^"]*") *$/i', $line);
                        $key = $this->arrayCheck($matches[1]);
                        $start = $i+1;
                    }
                } else {
                    $matches = $this->matchOrFail('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/', trim($line));
                    $content[trim($matches['name'])] = trim(trim($matches['value']), '"');
                }
            } else {
                // in a section
                if (strpos($line, $pretext . 'END') === 0) {
                    $this->addSection($key, $this->checkSubfile($this->path . '/' . $key, array_slice($structure, $start, $i - $start), $level+1, $subfiles));
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
    public function arrayCheck($title) {
        $title = trim($title);
        preg_match("/^\"\[i (\d+)\]\"$/i", $title, $matches);
        if ($matches) {
            $this->array = true;
            $title = (int) $matches[1];
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
    public function matchOrFail($regex, $line) {
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
    public function checkSubfile($path, $content, $level, $subfiles) {
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
    public function addSection($key, $object) {
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
    public function processSingleLineSection($line) {
        $matches = $this->matchOrFail('/^ *BEGIN ([^ "]*|"[^"]*")(?: +(.*))? +END *$/i', $line);
        $title = $this->arrayCheck($matches[1]);
        $content = isset($matches[2]) ? trim($matches[2]) : '';
        if ($content == '') {
            $this->addSection($title, new Section($this->path . '/' . $title, array()));
        } else {
            preg_match_all('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/i', $content, $matches);
            $this->addSection($title, new Section($this->path . '/' . $title, $matches[0]));
        }
    }

    /**
     * Check if a specific subsection exists.
     *
     * Checking for existence of a subsection requires the same work as retrieving it, so we attempt
     * that and see if we get anything back.
     *
     * @param string $path a relative path to a subsection
     *
     * @return bool
     */
    public function sectionExists($path) {
        try {
            $section = $this->getSection($path);
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
     * @param string $path a relative path to a subsection
     *
     * @return Section the specific unique section requested
     */
    public function getUniqueSection($path) {
        $section = $this->getSection($path);
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
     * @param string $path a relative path to a subsection
     *
     * @return Section[] the specific repeatable section requested
     */
    public function getRepeatableSection($path) {
        $section = $this->getSection($path);
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
     * @param string $path a relative path to a subsection
     *
     * @return Section|Section[] the specific subsection(s) requested or an empty array if not found
     */
    public function getSection($path) {
        $content = $this; // Relative pathing starts from the current section.
        // Move down the path until we find what we want or a part of the path is missing.
        foreach (explode('/', $path) as $section) {
            if (!isset($content->sections[$section])) {
                throw new SectionNotFoundException($path);
            }
            $content = $content->sections[$section];
        }

        return $content;
    }

    /**
     * Convert the section into a string.
     *
     * @param string $path     the label to assign to the section
     * @param int    $level    the indentation level
     * @param int    $width    the column width to pad the label to
     * @param bool   $in_array is this section part of an array?
     *
     * @return string The section converted to a string
     */
    public function toString($path = '', $level = -1, $width = 1, $in_array = false) {
        $contentKeyLength = 2;
        $sectionKeyLength = 1;
        foreach (array_keys($this->content) as $contentKey) {
            $length = (int) ceil(strlen($contentKey)/10);
            if ($length > $contentKeyLength) {
                $contentKeyLength = $length;
            }
        }
        foreach (array_keys($this->sections) as $section_key) {
            $length = (int) ceil(strlen($section_key)/10);
            if ($length > $sectionKeyLength) {
                $sectionKeyLength = $length;
            }
        }

        $string = '';
        $end = '';

        if ($level !== -1) {
            if ($in_array) {
                $path = "\"[i $path]\" ";
                $string = $this->indent($level, 'BEGIN ' . str_pad($path, 10*$width+2) . ' ');
            } else {
                $string = $this->indent($level, 'BEGIN ' . str_pad($path, 10*$width) . ' ');
            }
            $end = $this->indent($level, "END\n");
        }

        if ($this->sections === array() && $this->content === array()) {
            if ($in_array) {
                return $this->indent($level, 'BEGIN ' . str_pad($path, 10*$width+2) . " END\n");
            }

            return $this->indent($level, 'BEGIN ' . str_pad($path, 10*$width) . " END\n");
        }

        if (count($this->sections) == 0 && count($this->content) < 11) {
            $end = "END\n";
            foreach ($this->content as $key => $content) {
                $string .= "$key " . $this->quote($content) . '  ';
            }
        } else {
            foreach ($this->content as $key => $content) {
                $string .= "\n" . $this->indent($level + 1, str_pad($key, 10*$contentKeyLength) . ' ' . $this->quote($content) . '  ');
            }
            $string .= "\n";
            foreach ($this->sections as $key => $section) {
                if (is_array($section)) {
                    foreach ($section as $sub) {
                        $string .= $sub->toString($key, $level + 1, $sectionKeyLength, $this->array);
                    }
                } else {
                    $string .= $section->toString($key, $level + 1, $sectionKeyLength, $this->array);
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
