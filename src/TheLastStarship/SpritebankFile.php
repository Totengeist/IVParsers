<?php
/**
 * Classes necessary for processing a spritebank.txt file.
 */

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\IVFile;
use Totengeist\IVParser\Section;

/**
 * Classes necessary for processing a `.ship` file.
 */
class SpritebankFile extends IVFile {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $REQUIRED_SECTIONS = array('Sprite');
    protected static $FILE_TYPE = 'application/tls-spritebank+introversion';

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid ship file before calling the standard constructor.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array('/Sprite' => 'Totengeist\IVParser\TheLastStarship\Sprite')) {
        parent::__construct($structure, $level, $subfiles);
        if (!$this->is_valid() && $structure !== array()) {
            throw new InvalidFileException('spritebank');
        }
    }

    /**
     * Get a sprite.
     *
     * @param string $item the sprite to get
     *
     * @return Sprite|null the sprite
     */
    public function get_sprite($item) {
        foreach ($this->get_sprites() as $sprite) {
            if ($sprite->content['Name'] == $item) {
                return $sprite;
            }
        }

        return null;
    }

    /**
     * Get all sprites.
     *
     * @return Sprite[] all sprites
     */
    public function get_sprites() {
        /** @var Sprite[] $sprites */
        $sprites = $this->get_repeatable_section('Sprite');

        return $sprites;
    }
}

/**
 * Classes necessary for processing a `.ship` file.
 */
class Sprite extends Section {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $REQUIRED_SECTIONS = array('Frames');

    /**
     * An intermediary constructor.
     *
     * @param string[] $structure the structure of the section and its subsections
     * @param int      $level     the indentation level of the section in the original file
     * @param string[] $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct('', $structure, $level, $subfiles);
    }

    /**
     * Get sprite dimensions.
     *
     * @return array<array<string, int>> the sprite dimensions
     */
    public function get_metadata() {
        $metadata = array();
        $frames = $this->get_unique_section('Frames');
        foreach ($frames->sections as $key => $frame) {
            if (is_array($frame) || $frame->content == array()) {
                continue;
            }
            $frame_meta = array();
            $frame_meta['x'] = (int) $frame->content['X'];
            $frame_meta['y'] = (int) $frame->content['Y'];
            $frame_meta['height'] = (int) $frame->content['Height'];
            $frame_meta['width'] = (int) $frame->content['Width'];
            if (isset($frame->content['ExtendUp'])) {
                $frame_meta['y'] -= (int) $frame->content['ExtendUp'];
                $frame_meta['height'] += (int) $frame->content['ExtendUp'];
            }
            if (isset($frame->content['ExtendDown'])) {
                $frame_meta['height'] += (int) $frame->content['ExtendDown'];
            }
            if (isset($frame->content['ExtendLeft'])) {
                $frame_meta['x'] -= (int) $frame->content['ExtendLeft'];
                $frame_meta['width'] += (int) $frame->content['ExtendLeft'];
            }
            if (isset($frame->content['ExtendRight'])) {
                $frame_meta['width'] += (int) $frame->content['ExtendRight'];
            }
            $metadata[$key] = $frame_meta;
        }

        return $metadata;
    }
}
