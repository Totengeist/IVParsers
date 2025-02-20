# Introversion Parsers
[![Build Status](https://github.com/Totengeist/IVParsers/actions/workflows/tests.yml/badge.svg)](https://github.com/Totengeist/IVParsers/actions/workflows/tests.yml) [![Latest Stable Version](https://poser.pugx.org/totengeist/iv-parsers/v)](https://packagist.org/packages/totengeist/iv-parsers) [![codecov](https://codecov.io/gh/Totengeist/IVParsers/branch/main/graph/badge.svg?token=LBY3KQNRTG)](https://codecov.io/gh/Totengeist/IVParsers) [![Total Downloads](https://poser.pugx.org/totengeist/iv-parsers/downloads)](https://packagist.org/packages/totengeist/iv-parsers) [![Latest Unstable Version](https://poser.pugx.org/totengeist/iv-parsers/v/unstable)](https://packagist.org/packages/totengeist/iv-parsers) [![License](https://poser.pugx.org/totengeist/iv-parsers/license)](https://packagist.org/packages/totengeist/iv-parsers) [![PHP Version Require](https://poser.pugx.org/totengeist/iv-parsers/require/php)](https://packagist.org/packages/totengeist/iv-parsers)

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
