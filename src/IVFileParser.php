<?php

namespace IVParser;

class Section {
    public $path = null;
    public $content = null;
    public $sections = [];

    public function __construct($path = null, $content = null, $level = 0, $subfiles = []) {
        if ($path !== null) {
            $this->path = $path;
        }
        if ($content !== null) {
            $this->get_sections($content, $level, $subfiles);
        }
    }

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

    public function get_sections($structure, $level = 0, $subfiles = []) {
        $content = [];
        $start = -1;
        $key = null;
        for ($i = 0; $i < count($structure); $i++) {
            $line = $structure[$i];
            if (trim($line) == '') {
                continue;
            }
            $pretext = str_repeat('    ', $level);
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
                            $this->addSection($section_title, new Section($this->path . '/' . $section_title, []));
                        } else {
                            preg_match_all('/((?<name>[^ "]+|"[^"]+") +(?<value>[^ "]+|"[^"]+"))/i', $section_content, $content_check);
                            $this->addSection($section_title, new Section($this->path . '/' . $section_title, $content_check[0]));
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
                    $this->addSection($key, $this->checkSubfile($this->path . '/' . $key, array_slice($structure, $start, $i - $start), $level+1, $subfiles));
                    $start = -1;
                }
            }
        }
        $this->content = $content;
    }

    public function checkSubfile($path, $content, $level, $subfiles) {
        if (in_array($path, array_keys($subfiles))) {
            return new $subfiles[$path]($content, $level, $subfiles);
        }

        return new Section($path, $content, $level, $subfiles);
    }

    /**
     * Returns a Section object or an array of Section objects.
     */
    public function addSection($key, $object) {
        if (isset($this->sections[$key])) {
            if (gettype($this->sections[$key]) == 'array') {
                $this->sections[$key][] = $object;
            } else {
                $this->sections[$key] = [$this->sections[$key]];
            }
        } else {
            $this->sections[$key] = $object;
        }
    }

    public function section_exists($section_path) {
        $path = explode('/', $section_path);
        $content = $this;
        foreach ($path as $section) {
            if (isset($content->sections[$section])) {
                $content = $content->sections[$section];
            } else {
                return false;
            }
        }

        return true;
    }

    public function get_section($section_path) {
        $path = explode('/', $section_path);
        $content = $this;
        foreach ($path as $section) {
            $content = $content->sections[$section];
        }

        return $content;
    }
}

class IVFile extends Section {
    public $file = null;

    public function __construct($structure = null, $level = 0, $subfiles = []) {
        if ($structure !== null) {
        	if( is_string($structure) ) {
        		$structure = preg_split('/\r?\n/', $structure);
        	}
            $this->evaluate($structure, $level, $subfiles);
        }
    }

    public function evaluate($structure, $level = 0, $subfiles = []) {
        parent::__construct('', $structure, $level, $subfiles);
    }
}
