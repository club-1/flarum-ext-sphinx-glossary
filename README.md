# Sphinx Glossary

![License](https://img.shields.io/badge/license-AGPL--3.0--or--later-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/club-1/flarum-ext-sphinx-glossary.svg)](https://packagist.org/packages/club-1/flarum-ext-sphinx-glossary) [![Total Downloads](https://img.shields.io/packagist/dt/club-1/flarum-ext-sphinx-glossary.svg)](https://packagist.org/packages/club-1/flarum-ext-sphinx-glossary)

A [Flarum](http://flarum.org) extension. Add links to the definition of terms defined in a [Sphinx documentation](https://www.sphinx-doc.org/) inventory.

For now this extension does not have any GUI admin panel settings.
It can only be configured through the [Flarum console](https://docs.flarum.org/console/).

This extension is based on the [intersphinx feature](https://www.sphinx-doc.org/en/master/usage/extensions/intersphinx.html) of Sphinx and is configured in a similar fashion.
Mappings that resemble the [`intersphinx_mapping` configuration value](https://www.sphinx-doc.org/en/master/usage/extensions/intersphinx.html#confval-intersphinx_mapping)
can be added to the database using the `sphinx:add` command.
Then the `sphinx:update` command fetches and parses the corresponding inventories to populate the glossary.

By default only the `std:term` roles are used as glossary entries.
This can be changed on a per mapping basis, using the `--role=ROLE` option.
For example:

    php flarum sphinx:add club1 https://club1.fr/docs/fr/ --role=term --role=logiciel --role=commande

## Installation

Install with composer:

```sh
composer require club-1/flarum-ext-sphinx-glossary:"*"
```

## Usage

After enabling the extension from the admin panel, the following Flarum commands are available:

    sphinx:add      Add a Sphinx documentation inventory to the mapping list
    sphinx:list     List the Sphinx inventory mappings
    sphinx:remove   Remove a Sphinx documentation inventory from the mapping list
    sphinx:update   Update Sphinx glossary entries by downloading the latest inventories

## Updating

```sh
composer update club-1/flarum-ext-sphinx-glossary:"*"
php flarum migrate
php flarum cache:clear
```

## Acknowledgement

This extension is based on the following libraries:

- [s9e\TextFormatter](https://github.com/s9e/TextFormatter)'s [Keyword plugin](https://s9etextformatter.readthedocs.io/Plugins/Keywords/Synopsis/)
- [PHP Sphinx Inventory Parser](https://github.com/club-1/sphinx-inventory-parser)

## Links

- [Packagist](https://packagist.org/packages/club-1/flarum-ext-sphinx-glossary)
- [GitHub](https://github.com/club-1/flarum-ext-sphinx-glossary)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)
-->
