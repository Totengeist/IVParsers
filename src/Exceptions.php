<?php

namespace Totengeist\IVParser\Exception;

/**
 * Exception that is raised when a queried section is not found.
 */
class SectionNotFoundException extends \Exception {
    /**
     * @param string $message the error message to display
     */
    public function __construct($message = '') {
        parent::__construct($message);
    }
}

/**
 * Exception that is raised when a file is determined to be invalid.
 */
class InvalidFileException extends \Exception {
    /**
     * @param string $file_type the type of file that was to be loaded
     */
    public function __construct($file_type = 'Introversion') {
        parent::__construct("This is not a valid $file_type file.");
    }
}
