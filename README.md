Scoped EAV
========================

## What is Scoped EAV Magento 2 Module?

This module allow possibility to create custom EAV (Entity Attribute Value model) to describe entities where the number of attributes (properties and parameters) which can be used to describe it.

## Compatibility

| Module version | Magento | Mage-OS | PHP |
| --- | --- | --- | --- |
| 1.4.x | 2.4.6 and later (tested up to 2.4.9) | 1.0.0 and later (tested up to 4.0) | 8.1 to 8.5, as supported by the platform |
| 1.3.x, from 1.3.11 | 2.4.4 and 2.4.5 | not available | 8.1 |

Releases up to 1.3.10 are not covered by this table: they declared `php ^7.4 || ^8.1` and any `magento/framework` version, and their method signatures use native `mixed` or union types, which the code generator of Magento 2.4.4 and 2.4.5 rejects (`setup:di:compile` fails).

## Who is developing this module ?

![Smile](doc/static/smile.png)

This module is built by Smile.

Smile is the **European leader of Open Source** and also a four-times **Europe Partner of the the Year** (2010-2014), two-times **Spirit of Excellence** (2015-2016), **Global Elite Partner** (2017-now), and **Regional Commercial Partner** (2018) awarded by Magento.

Our multidisciplinary team brings together experts in technology, innovation, and new applications.

Together we explore, invent, and test technologies of the future, to better serve our clients.

## Contributing

You can contribute to this module by submitting issues or pull requests.

For more details, please take a look at the [contribution guidelines](CONTRIBUTING.md).

## Changelog

All notable changes are recorded in this [changelog](CHANGELOG.md).

## Standards & Code Quality

[Smile Lab Quality Suite (Magento)](https://github.com/Smile-SA/magento2-smilelab-quality-suite/) provides coding standards / rulesets that can be used on Magento projects.
