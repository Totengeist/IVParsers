<?php

/**
 * Classes necessary for processing a recipes.txt file.
 */

namespace Totengeist\IVParser\TheLastStarship;

use Totengeist\IVParser\Exception\InvalidFileException;
use Totengeist\IVParser\IVFile;

/**
 * Classes necessary for processing a `.ship` file.
 */
class RecipesFile extends IVFile {
    /** @var string[] paths of sections that must exist for it to be a valid file */
    protected static $REQUIRED_SECTIONS = array('Recipe');
    protected static $FILE_TYPE = 'application/tls-recipes+introversion';

    /**
     * An intermediary constructor.
     *
     * Verify the file is a valid ship file before calling the standard constructor.
     *
     * @param string|string[] $structure the structure of the section and its subsections
     * @param int             $level     the indentation level of the section in the original file
     * @param string[]        $subfiles  an array of IVFile-inheriting classes and their paths
     */
    public function __construct($structure = array(), $level = 0, $subfiles = array()) {
        parent::__construct($structure, $level, $subfiles);
        if (!$this->isValid() && $structure !== array()) {
            throw new InvalidFileException('recipes');
        }
    }

    /**
     * Get a definition.
     *
     * @param string $equipment the type of equipment to get
     *
     * @return \Totengeist\IVParser\Section[] the definition section
     */
    public function getRecipesByEquipment($equipment) {
        $recipes = array();
        foreach ($this->getRepeatableSection('Recipe') as $recipe) {
            if (!isset($recipe->content['Equipment'])) {
                if ($recipe->content['Type'] == $equipment) {
                    $recipes[] = $recipe;
                }
            } else {
                if ($recipe->content['Equipment'] == $equipment) {
                    $recipes[] = $recipe;
                }
            }
        }

        return $recipes;
    }

    /**
     * Get all recipes.
     *
     * @return \Totengeist\IVParser\Section[] the recipes
     */
    public function getRecipes() {
        return $this->getRepeatableSection('Recipe');
    }
}
