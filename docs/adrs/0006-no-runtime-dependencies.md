# Require no runtime Composer dependencies

Date: 2026-11-15

_Drafted with the `adr-suggestions` AI skill._

## Status

Proposed

## Context

The library runs from a site's `settings.php` (`README.md:80`), before Drupal's container exists, so it has to be cheap to load and mustn't constrain the versions of packages a site installs. `composer.json` has required only PHP since it was added in `bc15358` (2023-06-24).

_The reason above was suggested by the `adr-suggestions` AI skill, without a written source, and chosen by the maintainer. Someone who was part of the decision should check it, then remove this line._

<!-- TODO: Alternatives considered? -->

## Decision

We will keep `composer.json`'s `require` to PHP alone, and read the environment with PHP's own functions such as `getenv()`. Development tools go in `require-dev`.

## Consequences

<!-- TODO: Consequences? -->
