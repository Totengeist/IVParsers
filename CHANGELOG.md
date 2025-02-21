# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.0]

### Added

- Support exporting files.
- Support more file types for The Last Starship (spritebank, definitions, recipes).
- File type determination and media types.
- Notepad++ compatible language definition for viewing Introversion files.
- Infrastructure for bug checking (and a fix for the [Tiddlet Bug](https://www.tls-wiki.com/wiki/The_trouble_with_Tiddlets#Bugs)).
- Infrastructure for compatibility checking.
- Metadata editing for The Last Starship Ship and Save files.
- This CHANGELOG.

### Changed

- Stability fixes and refactoring.
- Standardize API with PHP standards.
- Create subdirectory for The Last Starship to improve organization for future support of other games.

### Removed

- Obsolete output functions used for debugging.
- PHP-CS-Fixer and phpstan development dependencies.

## [0.1.1] - 2023-05-11

### Added

- Unit tests

### Changed

- Expand [README](./README.md) to include basic technical information.
- Throw proper exceptions instead of printing errors to the console.
- Expand compatibility from PHP 5.3 to 8.3

## [0.1.0] - 2023-02-10

### Added

- Basic parser for Introversion files, including classes for Save and Ship files for The Last Starship.
- Basic [README](./README.md) and valid [LICENSE](LICENSE).

[unreleased]: https://github.com/Totengeist/IVParsers/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/Totengeist/IVParsers/releases/tag/v0.2.0
[0.1.1]: https://github.com/Totengeist/IVParsers/releases/tag/v0.1.1
[0.1.0]: https://github.com/Totengeist/IVParsers/releases/tag/v0.1.0
