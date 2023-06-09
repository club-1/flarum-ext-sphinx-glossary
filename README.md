# CLUB1 Sphinx Glossary

![License](https://img.shields.io/badge/license-AGPL--3.0--or--later-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/club-1/flarum-ext-sphinx-glossary.svg)](https://packagist.org/packages/club-1/flarum-ext-sphinx-glossary) [![Total Downloads](https://img.shields.io/packagist/dt/club-1/flarum-ext-sphinx-glossary.svg)](https://packagist.org/packages/club-1/flarum-ext-sphinx-glossary) [![Coverage](https://img.shields.io/codecov/c/gh/club-1/flarum-ext-sphinx-glossary/main?token=HFJQS4C1JK)](https://codecov.io/gh/club-1/flarum-ext-sphinx-glossary) [![Build Status](https://img.shields.io/github/actions/workflow/status/club-1/flarum-ext-sphinx-glossary/check.yml?branch=main)](https://github.com/club-1/flarum-ext-sphinx-glossary/actions/workflows/check.yml)

A [Flarum](http://flarum.org) extension. Add links to the definition of terms defined in a [Sphinx documentation](https://www.sphinx-doc.org/) inventory.

![demo image](https://static.club1.fr/nicolas/projects/flarum-ext-sphinx-glossary/banner.png)

This is a quite niche extension and it is still a little bit rough around the edges but it works nicely. It could be useful if your community is gathered around a software project that defines a bunch of terms in a Sphinx documentation.

For now it does not have any GUI admin panel settings. It can only be configured through the [Flarum console](https://docs.flarum.org/console/).

This extension is based on the [intersphinx feature](https://www.sphinx-doc.org/en/master/usage/extensions/intersphinx.html) of Sphinx and is configured in a similar fashion. Mappings that resemble the [`intersphinx_mapping` configuration value](https://www.sphinx-doc.org/en/master/usage/extensions/intersphinx.html#confval-intersphinx_mapping) can be added to the database using the `sphinx:add` command. Then the `sphinx:update` command fetches and parses the corresponding inventories to populate the glossary. This command is scheduled to run daily. It uses HTTP caching and PHP streams to stay low on resources.

By default only the `std:term` roles are used as glossary entries. This can be changed on a per mapping basis, using the `--role=ROLE` option. For example:

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
    sphinx:objects  Display info about the loaded Sphinx objects
    sphinx:remove   Remove a Sphinx documentation inventory from the mapping list and all its objects
    sphinx:update   Update Sphinx glossary entries by downloading the latest inventories

### Recommendation

This extension alone does not apply the formatting changes to previously posted comments. I you want to reparse all the comments posts of the database it is recommended to install and enable the [`club-1/flarum-ext-chore-commands`](https://github.com/club-1/flarum-ext-chore-commands) extension and use its `chore:reparse` command.

## Updating

```sh
composer update club-1/flarum-ext-sphinx-glossary:"*"
php flarum migrate
php flarum cache:clear
```

## Development

The following tools are needed for development:

- [Make](https://www.gnu.org/software/make/)
- [Composer](https://getcomposer.org/)
- [BusyBox](https://busybox.net/)

## Acknowledgement

This extension is based on the following libraries:

- [s9e\TextFormatter](https://github.com/s9e/TextFormatter)'s [Keyword plugin](https://s9etextformatter.readthedocs.io/Plugins/Keywords/Synopsis/)
- [PHP Sphinx Inventory Parser](https://github.com/club-1/sphinx-inventory-parser)

## Links

- [Packagist](https://packagist.org/packages/club-1/flarum-ext-sphinx-glossary)
- [GitHub](https://github.com/club-1/flarum-ext-sphinx-glossary)
- [Discuss](https://discuss.flarum.org/d/32764)
