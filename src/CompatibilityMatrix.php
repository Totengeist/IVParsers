<?php

/**
 * Base class for verifying compatibility with various versions of the game.
 */

namespace Totengeist\IVParser;

use Totengeist\IVParser\Exception\InvalidFileException;

/**
 * Base class for compatibility calculations.
 */
class CompatibilityMatrix {
    /**
     * @var array<string, mixed>
     */
    public $matrix = array();

    /**
     * Build the compatibility matrix.
     *
     * @param string $json the JSON to build the matrix from
     *
     * @return void
     */
    public function build_compatibility_matrix($json) {
        /** @var array<string, array<string, array<string, array<string, string[]>>>>|null $initial */
        $initial = json_decode($json, true);

        if ($initial == null || !isset($initial['versions'])) {
            throw new InvalidFileException('compatibility matrix');
        }

        $previous = array();
        foreach ($initial['versions'] as $version => $categories) {
            foreach ($categories as $category => $data) {
                if (isset($previous[$category])) {
                    $previous[$category] = $this->update_category($data, $previous[$category]);
                } else {
                    $previous[$category] = $this->update_category($data);
                }
            }
            $this->matrix[$version] = $previous;
        }
    }

    /**
     * Perform diff'ing on a compatibility category.
     *
     * @param array<string,string[]> $diff     the data to update
     * @param string[]|null          $original the original data
     *
     * @return string[]
     */
    public function update_category($diff, $original = null) {
        if ($original == null) {
            $original = array();
        }
        if (isset($diff['_'])) {
            return array_unique($diff['_']);
        }
        // remove ambiguous duplicates
        if (isset($diff['-']) && isset($diff['+'])) {
            $minus = array_unique($diff['-']);
            $plus = array_unique($diff['+']);
            $diff['-'] = array_diff($minus, $plus);
            $diff['+'] = array_diff($plus, $minus);
        }
        if (isset($diff['-'])) {
            $original = array_diff($original, array_unique($diff['-']));
        }
        if (isset($diff['+'])) {
            $original = array_merge($original, $diff['+']);
        }

        /* @var string[] $original */
        return array_unique($original);
    }
}
