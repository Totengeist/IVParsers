# Introversion Parsers
[![Build Status](https://github.com/Totengeist/IVParsers/actions/workflows/tests.yml/badge.svg)](https://github.com/Totengeist/IVParsers/actions/workflows/tests.yml) [![Latest Stable Version](https://img.shields.io/packagist/v/totengeist/iv-parsers)][4] [![codecov](https://codecov.io/gh/Totengeist/IVParsers/branch/main/graph/badge.svg?token=LBY3KQNRTG)](https://codecov.io/gh/Totengeist/IVParsers) [![Total Downloads](https://img.shields.io/packagist/dt/totengeist/iv-parsers)][4] [![License](https://img.shields.io/packagist/l/totengeist/iv-parsers)][4] [![PHP Version Require](https://img.shields.io/packagist/dependency-v/totengeist/iv-parsers/php)][4]

This library aims to provide parsers for common Introversion file formats. It is currently focused
on the early access game [The Last Starship][1], but will hopefully include other parsers in the future.

This library is developed by the community. We are not affiliated with Introversion Software.

## Installation

Use the package manager [composer][2] to install IVParsers.

```bash
composer require totengeist/iv-parsers
```

## Usage

IVParsers currently supports `.ship` and `.space` files for The Last Starship. Files can be loaded, modified and saved.

```php
$ship = ShipFile(file_get_contents('science-vessel.ship'));
$ship->setName('Crusher');
$ship->setAuthor('Totengeist');
file_put_contents('Crusher.ship', $ship->toString());
```

```php
$save = SaveFile(file_get_contents('Fun Time.space'));
$save->getSaveVersion();
TiddletBug::resolveBug($save);
file_put_contents('Fun Time.space', $save->toString());
```

## Support

For questions not related to contributing directly to the project, please reach out on [Discord][3].

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](./LICENSE)


 [1]: https://steamcommunity.com/app/1857080
 [2]: https://getcomposer.org/download/
 [3]: https://discord.gg/AcCgj3T5sH
 [4]: https://packagist.org/packages/totengeist/iv-parsers
